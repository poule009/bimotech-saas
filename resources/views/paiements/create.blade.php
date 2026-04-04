<x-app-layout>
    <x-slot name="header">Nouveau paiement</x-slot>

<style>
/* ── LAYOUT ── */
.form-grid { display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start; }

/* ── CARDS ── */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:18px; }
.card:last-child { margin-bottom:0; }
.card-hd { padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:10px; }
.card-icon { width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:16px;height:16px; }
.card-icon.gold { background:#f5e9c9; }
.card-icon.gold svg { color:#8a6e2f; }
.card-icon.blue { background:#dbeafe; }
.card-icon.blue svg { color:#1d4ed8; }
.card-icon.green { background:#dcfce7; }
.card-icon.green svg { color:#16a34a; }
.card-icon.gray { background:#f3f4f6; }
.card-icon.gray svg { color:#6b7280; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-body { padding:20px; }

/* ── FORM ELEMENTS ── */
.form-group { margin-bottom:16px; }
.form-group:last-child { margin-bottom:0; }
.form-label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-label .req { color:#dc2626; margin-left:2px; }
.form-label .opt { font-size:11px; font-weight:400; color:#9ca3af; margin-left:4px; }

.form-input, .form-select, .form-textarea {
    width:100%; padding:9px 13px;
    border:1px solid #e5e7eb; border-radius:8px;
    font-size:13px; color:#0d1117; font-family:'DM Sans',sans-serif;
    background:#fff; outline:none;
    transition:border-color .15s, box-shadow .15s;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color:#c9a84c;
    box-shadow:0 0 0 3px rgba(201,168,76,0.10);
}
.form-input.error, .form-select.error { border-color:#dc2626; }
.form-textarea { resize:vertical; min-height:80px; }
.form-select { cursor:pointer; }

.form-hint { font-size:11px; color:#9ca3af; margin-top:5px; display:flex; align-items:center; gap:4px; }
.form-hint svg { width:11px;height:11px;flex-shrink:0; }
.form-hint.success { color:#16a34a; }
.form-hint.warning { color:#d97706; }

.form-error { font-size:12px; color:#dc2626; margin-top:5px; font-weight:500; display:flex; align-items:center; gap:4px; }
.form-error svg { width:12px;height:12px;flex-shrink:0; }

.form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.form-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }

/* ── CONTRAT SELECTOR ── */
.contrat-search-wrap { position:relative; }
.contrat-search { padding-left:36px !important; }
.contrat-search-icon { position:absolute;left:11px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af; pointer-events:none; }
.contrat-dropdown {
    position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:50;
    background:#fff;border:1px solid #e5e7eb;border-radius:10px;
    box-shadow:0 8px 24px -4px rgba(0,0,0,0.12);
    max-height:240px;overflow-y:auto;display:none;
}
.contrat-option { padding:10px 14px; cursor:pointer; border-bottom:1px solid #f3f4f6; transition:background .1s; }
.contrat-option:last-child { border-bottom:none; }
.contrat-option:hover, .contrat-option.highlighted { background:#f9fafb; }
.contrat-option-ref { font-size:12px; font-weight:600; color:#0d1117; }
.contrat-option-sub { font-size:11px; color:#6b7280; margin-top:1px; }
.contrat-option-loyer { font-family:'Syne',sans-serif; font-size:12px; font-weight:600; color:#8a6e2f; }

/* ── CONTRAT SÉLECTIONNÉ ── */
.contrat-card {
    background:#0d1117; border-radius:10px; padding:16px 18px;
    margin-top:10px; display:none;
}
.contrat-card-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.cc-lbl { font-size:9px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:3px; }
.cc-val { font-size:13px;font-weight:600;color:#fff; }
.cc-val.gold { color:#c9a84c; }

/* ── APERÇU CALCUL (sticky) ── */
.apercu-card {
    background:#0d1117; border-radius:14px; overflow:hidden; position:sticky; top:80px;
}
.apercu-hd { padding:16px 20px; border-bottom:1px solid rgba(255,255,255,.07); }
.apercu-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#fff; }
.apercu-sub { font-size:11px; color:rgba(255,255,255,.4); margin-top:2px; }
.apercu-body { padding:16px 20px; }

.ap-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.05); }
.ap-row:last-child { border-bottom:none; }
.ap-lbl { font-size:12px; color:rgba(255,255,255,.5); }
.ap-val { font-family:'Syne',sans-serif; font-size:13px; font-weight:600; color:#fff; }
.ap-val.green { color:#4ade80; }
.ap-val.gold  { color:#c9a84c; }
.ap-val.muted { color:rgba(255,255,255,.3); }

.ap-sep { height:1px; background:rgba(255,255,255,.08); margin:4px 0; }

.ap-total { background:rgba(74,222,128,.08); border:1px solid rgba(74,222,128,.15); border-radius:9px; padding:14px 16px; margin-top:14px; }
.ap-total-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:rgba(74,222,128,.6);margin-bottom:5px; }
.ap-total-val { font-family:'Syne',sans-serif; font-size:22px; font-weight:700; color:#4ade80; letter-spacing:-.5px; line-height:1; }
.ap-total-u { font-size:12px; color:rgba(74,222,128,.5); margin-left:3px; }

.ap-empty { padding:24px; text-align:center; }
.ap-empty-icon { width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.05);display:flex;align-items:center;justify-content:center;margin:0 auto 10px; }
.ap-empty-icon svg { width:18px;height:18px;color:rgba(255,255,255,.3); }
.ap-empty-text { font-size:12px;color:rgba(255,255,255,.3);line-height:1.5; }

/* ── PERIODE HINT ── */
.periode-hint { background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:8px 12px;margin-top:8px;font-size:11px;color:#16a34a;font-weight:500;display:none;align-items:center;gap:5px; }
.periode-hint svg { width:13px;height:13px;flex-shrink:0; }

/* ── CHECKBOX ── */
.checkbox-row { display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;cursor:pointer;transition:border-color .15s; }
.checkbox-row:hover { border-color:#c9a84c; }
.checkbox-row input[type=checkbox] { width:16px;height:16px;accent-color:#c9a84c;cursor:pointer;flex-shrink:0; }
.checkbox-row-label { font-size:13px;color:#374151;font-weight:500; }
.checkbox-row-sub { font-size:11px;color:#6b7280;margin-top:1px; }

/* ── MODE PAIEMENT PILLS ── */
.mode-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
.mode-pill { position:relative; }
.mode-pill input { position:absolute;opacity:0;width:0;height:0; }
.mode-pill label {
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    padding:10px 8px;border:1.5px solid #e5e7eb;border-radius:9px;
    cursor:pointer;transition:all .15s;gap:6px;
    font-size:11px;font-weight:500;color:#6b7280;text-align:center;
    background:#fff;
}
.mode-pill label svg { width:18px;height:18px; }
.mode-pill input:checked + label { border-color:#c9a84c;background:#f5e9c9;color:#8a6e2f; }
.mode-pill label:hover { border-color:#c9a84c;background:#fdf8ef; }

/* ── SUBMIT ── */
.submit-bar { display:flex;align-items:center;gap:12px;padding:16px 20px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-submit { flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 24px;background:#0d1117;color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:opacity .15s; }
.btn-submit:hover { opacity:.88; }
.btn-submit svg { width:16px;height:16px; }
.btn-cancel { padding:12px 20px;background:#fff;color:#6b7280;border:1px solid #e5e7eb;border-radius:9px;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:background .15s; }
.btn-cancel:hover { background:#f9fafb; }
</style>

<div style="padding:24px 32px 48px">

    {{-- HEADER --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:20px">
        <a href="{{ route('admin.paiements.index') }}" style="color:#6b7280;text-decoration:none">Paiements</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Nouveau paiement</span>
    </div>

    <div style="margin-bottom:22px">
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Enregistrer un paiement</h1>
        <p style="font-size:13px;color:#6b7280;margin-top:3px">Le calcul de commission est automatique dès que vous saisissez le loyer.</p>
    </div>

    <form method="POST" action="{{ route('admin.paiements.store') }}" id="form-paiement">
        @csrf

        <div class="form-grid">

            {{-- ═══ COLONNE GAUCHE ═══ --}}
            <div>

                {{-- SÉLECTION CONTRAT --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="card-title">Contrat concerné</div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="contrat_id" id="contrat_id" value="{{ old('contrat_id', $contratPreselectionne?->id) }}">

                        <div class="form-group">
                            <label class="form-label">Rechercher un contrat <span class="req">*</span></label>
                            <div class="contrat-search-wrap">
                                <svg class="contrat-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <input type="text" id="contrat-search"
                                    class="form-input contrat-search {{ $errors->has('contrat_id') ? 'error' : '' }}"
                                    placeholder="Nom locataire, référence bien…"
                                    autocomplete="off">
                                <div class="contrat-dropdown" id="contrat-dropdown"></div>
                            </div>
                            @error('contrat_id')
                                <div class="form-error">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Infos contrat sélectionné --}}
                        <div class="contrat-card" id="infos-contrat">
                            <div class="contrat-card-grid">
                                <div>
                                    <div class="cc-lbl">Bien</div>
                                    <div class="cc-val" id="info-bien">—</div>
                                </div>
                                <div>
                                    <div class="cc-lbl">Locataire</div>
                                    <div class="cc-val" id="info-locataire">—</div>
                                </div>
                                <div>
                                    <div class="cc-lbl">Loyer contractuel</div>
                                    <div class="cc-val gold" id="info-loyer">—</div>
                                </div>
                                <div>
                                    <div class="cc-lbl">Taux commission</div>
                                    <div class="cc-val gold" id="info-commission">—</div>
                                </div>
                            </div>
                            <div style="border-top:1px solid rgba(255,255,255,.07);margin-top:12px;padding-top:12px">
                                <div class="cc-lbl">Référence bail</div>
                                <div class="cc-val" style="font-family:monospace;font-size:12px" id="info-ref-bail">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PÉRIODE & DATE --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="card-title">Période & date</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Mois du loyer <span class="req">*</span></label>
                                <input type="month" name="periode" id="periode"
                                    class="form-input {{ $errors->has('periode') ? 'error' : '' }}"
                                    value="{{ old('periode', now()->format('Y-m')) }}">
                                <div class="periode-hint" id="periode-hint">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span id="periode-hint-text"></span>
                                </div>
                                @error('periode')
                                    <div class="form-error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date du paiement <span class="req">*</span></label>
                                <input type="date" name="date_paiement" id="date_paiement"
                                    class="form-input {{ $errors->has('date_paiement') ? 'error' : '' }}"
                                    value="{{ old('date_paiement', now()->format('Y-m-d')) }}"
                                    max="{{ now()->format('Y-m-d') }}">
                                @error('date_paiement')
                                    <div class="form-error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MONTANTS --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="card-title">Ventilation du loyer</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Loyer nu <span class="req">*</span> <span class="opt">(hors charges)</span></label>
                                <input type="number" name="loyer_nu" id="loyer_nu"
                                    class="form-input {{ $errors->has('loyer_nu') ? 'error' : '' }}"
                                    value="{{ old('loyer_nu') }}"
                                    placeholder="Ex: 200000" min="1" step="500"
                                    oninput="calculerApercu()">
                                @error('loyer_nu')
                                    <div class="form-error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Charges locatives <span class="opt">(optionnel)</span></label>
                                <input type="number" name="charges_amount" id="charges_amount"
                                    class="form-input"
                                    value="{{ old('charges_amount', 0) }}"
                                    placeholder="0" min="0" step="500"
                                    oninput="calculerApercu()">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">TOM <span class="opt">(Taxe Ordures Ménagères)</span></label>
                                <input type="number" name="tom_amount" id="tom_amount"
                                    class="form-input"
                                    value="{{ old('tom_amount', 0) }}"
                                    placeholder="0" min="0" step="100"
                                    oninput="calculerApercu()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Référence bail <span class="opt">(optionnel)</span></label>
                                <input type="text" name="reference_bail" id="reference_bail"
                                    class="form-input"
                                    value="{{ old('reference_bail') }}"
                                    placeholder="Ex: BAIL-DKR-2024">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODE DE PAIEMENT --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gray">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <div class="card-title">Mode de règlement</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="mode-grid">
                                @php
                                    $modes = [
                                        'especes'      => ['Espèces',      '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>'],
                                        'wave'         => ['Wave',         '<path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/>'],
                                        'orange_money' => ['Orange Money', '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3"/>'],
                                        'virement'     => ['Virement',     '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>'],
                                        'cheque'       => ['Chèque',       '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75a3.75 3.75 0 01-7.5 0V6a3.75 3.75 0 017.5 0zM11.25 3.744a3.737 3.737 0 012.5 0V6a3.75 3.75 0 11-7.5 0V3.744m-4.5 5.006a48.05 48.05 0 0114.25 0m-14.25 0a3 3 0 00-3 3v4.5a3 3 0 003 3h15a3 3 0 003-3v-4.5a3 3 0 00-3-3"/>'],
                                        'free_money'   => ['Free Money',   '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                                        'mobile_money' => ['Mobile',       '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5"/>'],
                                    ];
                                @endphp
                                @foreach($modes as $key => [$label, $icon])
                                <div class="mode-pill">
                                    <input type="radio" name="mode_paiement" id="mode_{{ $key }}"
                                        value="{{ $key }}"
                                        {{ old('mode_paiement', 'especes') === $key ? 'checked' : '' }}>
                                    <label for="mode_{{ $key }}">
                                        <svg style="width:18px;height:18px;stroke-width:1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">{{ $icon }}</svg>
                                        {{ $label }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('mode_paiement')
                                <div class="form-error" style="margin-top:8px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- CAUTION & OPTIONS --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gray">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="card-title">Options & caution</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="checkbox-row" for="est_premier_paiement" id="premier-toggle">
                                <input type="checkbox" name="est_premier_paiement" id="est_premier_paiement"
                                    value="1" {{ old('est_premier_paiement') ? 'checked' : '' }}
                                    onchange="toggleCaution(this.checked)">
                                <div>
                                    <div class="checkbox-row-label">Premier paiement (entrée)</div>
                                    <div class="checkbox-row-sub">Cochez si c'est le premier loyer du contrat</div>
                                </div>
                            </label>
                        </div>

                        <div class="form-group" id="caution-field" style="{{ old('est_premier_paiement') ? '' : 'display:none' }}">
                            <label class="form-label">Caution perçue <span class="opt">(1er paiement)</span></label>
                            <input type="number" name="caution_percue" id="caution_percue"
                                class="form-input"
                                value="{{ old('caution_percue', 0) }}"
                                placeholder="Montant de la caution" min="0" step="500">
                            @error('caution_percue')
                                <div class="form-error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Notes <span class="opt">(optionnel)</span></label>
                            <textarea name="notes" class="form-textarea" placeholder="Remarques, références chèque, numéro de transaction…">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="form-error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="submit-bar">
                        <a href="{{ route('admin.paiements.index') }}" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Valider l'encaissement
                        </button>
                    </div>
                </div>

            </div>{{-- fin colonne gauche --}}

            {{-- ═══ COLONNE DROITE : APERÇU CALCUL ═══ --}}
            <div>
                <div class="apercu-card">
                    <div class="apercu-hd">
                        <div class="apercu-title">Aperçu du calcul</div>
                        <div class="apercu-sub">Mis à jour en temps réel</div>
                    </div>
                    <div class="apercu-body" id="apercu-content">
                        <div class="ap-empty" id="apercu-empty">
                            <div class="ap-empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                            </div>
                            <div class="ap-empty-text">Sélectionnez un contrat et saisissez le loyer pour voir le calcul</div>
                        </div>
                        <div id="apercu-calcul" style="display:none">
                            <div class="ap-row">
                                <div class="ap-lbl">Loyer nu</div>
                                <div class="ap-val" id="ap-loyer-nu">— F</div>
                            </div>
                            <div class="ap-row" id="ap-charges-row" style="display:none">
                                <div class="ap-lbl">+ Charges</div>
                                <div class="ap-val" id="ap-charges">— F</div>
                            </div>
                            <div class="ap-row" id="ap-tom-row" style="display:none">
                                <div class="ap-lbl">+ TOM</div>
                                <div class="ap-val" id="ap-tom">— F</div>
                            </div>
                            <div class="ap-row">
                                <div class="ap-lbl" style="font-weight:600;color:rgba(255,255,255,.7)">Total encaissé</div>
                                <div class="ap-val" style="color:#fff;font-size:15px" id="ap-total-enc">— F</div>
                            </div>
                            <div class="ap-sep"></div>
                            <div class="ap-row">
                                <div class="ap-lbl">Commission HT (<span id="ap-taux">0</span>%)</div>
                                <div class="ap-val gold" id="ap-comm-ht">— F</div>
                            </div>
                            <div class="ap-row">
                                <div class="ap-lbl">TVA (18%)</div>
                                <div class="ap-val gold" id="ap-tva">— F</div>
                            </div>
                            <div class="ap-row">
                                <div class="ap-lbl">Commission TTC</div>
                                <div class="ap-val gold" id="ap-comm-ttc">— F</div>
                            </div>
                            <div class="ap-total">
                                <div class="ap-total-lbl">Net propriétaire</div>
                                <div class="ap-total-val"><span id="ap-net">0</span><span class="ap-total-u">FCFA</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /form-grid --}}
    </form>

</div>

<script>
// ── Données contrats côté serveur ─────────────────────────────────────
const contratsData = [
    @foreach($contrats as $c)
    {
        id:        {{ $c->id }},
        label:     {{ json_encode($c->locataire->name . ' — ' . $c->bien->reference . ' (' . number_format($c->loyer_contractuel, 0, ',', ' ') . ' F/mois)') }},
        loyer_nu:  {{ $c->loyer_nu ?? $c->loyer_contractuel }},
        charges:   {{ $c->charges_mensuelles ?? 0 }},
        tom:       {{ $c->tom_amount ?? 0 }},
        commission:{{ $c->bien->taux_commission }},
        bien:      {{ json_encode($c->bien->reference) }},
        locataire: {{ json_encode($c->locataire->name) }},
        ref_bail:  {{ json_encode($c->reference_bail ?? '') }},
    },
    @endforeach
];

let tauxCommission = 0;
let selectedContratId = null;
const CSRF = '{{ csrf_token() }}';
const URL_PERIODE = '{{ url("admin/paiements/dernier-periode") }}';

// ── Pré-remplir si contrat_id dans URL ───────────────────────────────
@if($contratPreselectionne)
(function() {
    const c = contratsData.find(x => x.id === {{ $contratPreselectionne->id }});
    if (c) selectionnerContrat(c);
})();
@endif

// ── Recherche contrat ─────────────────────────────────────────────────
const searchInput = document.getElementById('contrat-search');
const dropdown    = document.getElementById('contrat-dropdown');

searchInput.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    if (!q) { dropdown.style.display = 'none'; return; }

    const matches = contratsData.filter(c => c.label.toLowerCase().includes(q)).slice(0, 8);
    if (!matches.length) { dropdown.style.display = 'none'; return; }

    dropdown.innerHTML = matches.map(c => `
        <div class="contrat-option" data-id="${c.id}" onclick="selectionnerContratById(${c.id})">
            <div style="display:flex;justify-content:space-between;align-items:baseline;gap:8px">
                <div class="contrat-option-ref">${c.bien} · ${c.locataire}</div>
                <div class="contrat-option-loyer">${Math.round(c.loyer_nu + c.charges + c.tom).toLocaleString('fr-FR')} F</div>
            </div>
            <div class="contrat-option-sub">Contrat #${c.id}</div>
        </div>
    `).join('');
    dropdown.style.display = 'block';
});

document.addEventListener('click', e => {
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

function selectionnerContratById(id) {
    const c = contratsData.find(x => x.id === id);
    if (c) selectionnerContrat(c);
}

function selectionnerContrat(c) {
    selectedContratId = c.id;
    tauxCommission    = parseFloat(c.commission) || 0;

    document.getElementById('contrat_id').value   = c.id;
    searchInput.value = c.label;
    dropdown.style.display = 'none';

    // Remplir infos contrat
    document.getElementById('info-bien').textContent       = c.bien;
    document.getElementById('info-locataire').textContent  = c.locataire;
    document.getElementById('info-loyer').textContent      = Math.round(c.loyer_nu + c.charges + c.tom).toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('info-commission').textContent = c.commission + '%';
    document.getElementById('info-ref-bail').textContent   = c.ref_bail || 'Auto-généré';
    document.getElementById('infos-contrat').style.display = 'block';

    // Pré-remplir les montants
    const loyerInput = document.getElementById('loyer_nu');
    if (!loyerInput.value || loyerInput.value === '0') loyerInput.value = c.loyer_nu || 0;

    const chargesInput = document.getElementById('charges_amount');
    if (!chargesInput.value || chargesInput.value === '0') chargesInput.value = c.charges || 0;

    const tomInput = document.getElementById('tom_amount');
    if (!tomInput.value || tomInput.value === '0') tomInput.value = c.tom || 0;

    const refBailInput = document.getElementById('reference_bail');
    if (!refBailInput.value && c.ref_bail) refBailInput.value = c.ref_bail;

    document.getElementById('ap-taux').textContent = c.commission;
    calculerApercu();
    chargerPeriodeSuggeree(c.id);
}

// ── AJAX période suggérée ─────────────────────────────────────────────
async function chargerPeriodeSuggeree(contratId) {
    try {
        const r = await fetch(`${URL_PERIODE}/${contratId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!r.ok) return;
        const data = await r.json();
        if (data.prochaine_periode) {
            document.getElementById('periode').value = data.prochaine_periode;
            document.getElementById('periode-hint-text').textContent =
                'Période suggérée : ' + data.prochaine_periode + ' (mois suivant le dernier paiement)';
            document.getElementById('periode-hint').style.display = 'flex';
        }
    } catch(e) {}
}

// ── Calcul aperçu en temps réel ───────────────────────────────────────
function calculerApercu() {
    const loyerNu  = parseFloat(document.getElementById('loyer_nu').value)      || 0;
    const charges  = parseFloat(document.getElementById('charges_amount').value) || 0;
    const tom      = parseFloat(document.getElementById('tom_amount').value)     || 0;

    if (!loyerNu || !tauxCommission) {
        document.getElementById('apercu-empty').style.display  = 'block';
        document.getElementById('apercu-calcul').style.display = 'none';
        return;
    }

    const totalEncaisse = loyerNu + charges + tom;
    const commHT        = loyerNu * (tauxCommission / 100);
    const tva           = commHT * 0.18;
    const commTTC       = commHT + tva;
    const netProprio    = totalEncaisse - commTTC;

    const fmt = n => Math.round(n).toLocaleString('fr-FR') + ' F';

    document.getElementById('ap-loyer-nu').textContent   = fmt(loyerNu);
    document.getElementById('ap-total-enc').textContent  = fmt(totalEncaisse);
    document.getElementById('ap-comm-ht').textContent    = fmt(commHT);
    document.getElementById('ap-tva').textContent        = fmt(tva);
    document.getElementById('ap-comm-ttc').textContent   = fmt(commTTC);
    document.getElementById('ap-net').textContent        = Math.round(netProprio).toLocaleString('fr-FR');

    // Charges et TOM
    const chargesRow = document.getElementById('ap-charges-row');
    const tomRow     = document.getElementById('ap-tom-row');
    chargesRow.style.display = charges > 0 ? '' : 'none';
    tomRow.style.display     = tom > 0     ? '' : 'none';
    document.getElementById('ap-charges').textContent = fmt(charges);
    document.getElementById('ap-tom').textContent     = fmt(tom);

    document.getElementById('apercu-empty').style.display  = 'none';
    document.getElementById('apercu-calcul').style.display = 'block';
}

// ── Toggle caution ────────────────────────────────────────────────────
function toggleCaution(checked) {
    document.getElementById('caution-field').style.display = checked ? '' : 'none';
}
</script>

</x-app-layout>