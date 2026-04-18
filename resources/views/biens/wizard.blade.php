@extends('layouts.app')
@section('title', 'Nouveau bien — Assistant')
@section('breadcrumb', 'Biens › Assistant de création')

@section('content')
<style>
*, *::before, *::after { box-sizing: border-box; }

/* ═══════════════════════════════════════
   WIZARD — Barre de progression
   ═══════════════════════════════════════ */
.wz-progress {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 32px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 20px 28px;
}
.wz-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 1;
    cursor: default;
}
.wz-step-bubble {
    width: 38px; height: 38px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Syne', sans-serif;
    font-size: 15px; font-weight: 700;
    transition: all .25s;
    position: relative; z-index: 1;
    background: #f3f4f6;
    color: #9ca3af;
    border: 2px solid #e5e7eb;
}
.wz-step.active .wz-step-bubble {
    background: var(--ac, #c9a84c);
    color: #0d1117;
    border-color: var(--ac, #c9a84c);
    box-shadow: 0 0 0 4px rgba(201,168,76,.15);
}
.wz-step.done .wz-step-bubble {
    background: #16a34a;
    color: #fff;
    border-color: #16a34a;
}
.wz-step-label {
    margin-top: 8px;
    font-size: 11px;
    font-weight: 600;
    color: #9ca3af;
    text-align: center;
    line-height: 1.3;
    max-width: 90px;
}
.wz-step.active .wz-step-label { color: #0d1117; }
.wz-step.done .wz-step-label { color: #16a34a; }

.wz-connector {
    flex: 1;
    height: 2px;
    background: #e5e7eb;
    position: relative;
    top: -14px; /* align with bubble center */
    transition: background .25s;
    max-width: 100px;
}
.wz-connector.done { background: #16a34a; }

/* ═══════════════════════════════════════
   WIZARD — Corps (steps)
   ═══════════════════════════════════════ */
.wz-layout { display: grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start; }

.wz-step-content { display: none; }
.wz-step-content.active { display: block; }

/* Card standard */
.card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 16px; }
.card-hd {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex; align-items: center; gap: 12px;
}
.card-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.card-icon svg { width: 15px; height: 15px; }
.card-icon.gold   { background: #f5e9c9; color: #8a6e2f; }
.card-icon.blue   { background: #dbeafe; color: #1d4ed8; }
.card-icon.green  { background: #dcfce7; color: #16a34a; }
.card-title { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; color: #0d1117; }
.card-subtitle { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.card-body { padding: 20px; }

/* Formulaire */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { margin-bottom: 14px; }
.form-group:last-child { margin-bottom: 0; }
.form-label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; }
.req { color: #dc2626; }
.opt { color: #9ca3af; font-weight: 400; }
.form-input, .form-select, .form-textarea {
    width: 100%; padding: 9px 12px;
    border: 1px solid #e5e7eb; border-radius: 8px;
    font-size: 13px; font-family: 'DM Sans', sans-serif; color: #0d1117;
    background: #fff; outline: none; transition: border .15s;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color: #c9a84c;
    box-shadow: 0 0 0 3px rgba(201,168,76,.1);
}
.form-textarea { resize: vertical; min-height: 80px; }

/* Sélecteur de propriétaire/locataire (radio cards) */
.person-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 8px;
    max-height: 320px;
    overflow-y: auto;
    padding: 2px;
}
.person-card {
    padding: 12px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    transition: all .15s;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
}
.person-card:hover { border-color: #d1d5db; background: #fafafa; }
.person-card.selected {
    border-color: #c9a84c;
    background: rgba(201,168,76,.04);
}
.person-card input[type=radio] { display: none; }
.person-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700;
    flex-shrink: 0;
}
.person-name { font-size: 12.5px; font-weight: 600; color: #0d1117; }
.person-email { font-size: 10.5px; color: #9ca3af; margin-top: 1px; }
.person-check {
    margin-left: auto;
    width: 18px; height: 18px;
    border-radius: 50%;
    border: 2px solid #e5e7eb;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: all .15s;
}
.person-card.selected .person-check {
    background: #c9a84c;
    border-color: #c9a84c;
}

/* Sélection "optionnelle" pour locataire (step 3) */
.person-card.skip-option {
    border-style: dashed;
    border-color: #e5e7eb;
}
.person-card.skip-option.selected {
    border-color: #9ca3af;
    background: #f9fafb;
}

/* Barre d'actions navigation */
.wz-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-top: 1px solid #f3f4f6;
    background: #fafafa;
    border-radius: 0 0 14px 14px;
}
.btn-prev {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    background: #fff;
    color: #6b7280;
    font-size: 13px; font-weight: 500;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
}
.btn-prev:hover { background: #f3f4f6; }
.btn-next {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 20px;
    border: none;
    border-radius: 9px;
    background: #c9a84c;
    color: #0d1117;
    font-size: 13px; font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: all .15s;
}
.btn-next:hover { background: #b8953d; }
.btn-next:disabled { opacity: .5; cursor: not-allowed; }
.btn-submit-final {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 24px;
    border: none;
    border-radius: 9px;
    background: #16a34a;
    color: #fff;
    font-size: 13px; font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: all .15s;
}
.btn-submit-final:hover { background: #15803d; }

/* Panneau récap (sticky) */
.recap-panel {
    background: #0d1117;
    border-radius: 14px;
    overflow: hidden;
    position: sticky;
    top: 24px;
}
.recap-hd { padding: 14px 18px; border-bottom: 1px solid rgba(255,255,255,.07); }
.recap-title { font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; color: #fff; }
.recap-step-label { font-size: 10px; color: rgba(255,255,255,.3); margin-top: 2px; }
.recap-body { padding: 16px 18px; }
.rr { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,.05); }
.rr:last-child { border-bottom: none; }
.rr-lbl { font-size: 11px; color: rgba(255,255,255,.4); }
.rr-val { font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 600; color: #e6edf3; }
.rr-val.gold { color: #c9a84c; }
.rr-val.green { color: #4ade80; }
.rr-val.empty { color: rgba(255,255,255,.2); font-style: italic; font-weight: 400; font-family: 'DM Sans', sans-serif; }

.recap-section { margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,.06); }
.recap-section-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: rgba(255,255,255,.2); margin-bottom: 6px; }

/* Alerte de validation */
.wz-alert {
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 12px;
    margin-bottom: 14px;
    display: none;
}
.wz-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; display: flex; gap: 8px; align-items: flex-start; }
</style>

<div style="padding: 0 0 60px; max-width: 960px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <div>
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:6px">
                <a href="{{ route('admin.biens.index') }}" style="color:#6b7280;text-decoration:none">Biens</a>
                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span style="color:#0d1117;font-weight:500">Assistant de création</span>
            </div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px;margin:0">
                Nouveau bien immobilier
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:4px">3 étapes guidées pour ne rien oublier</p>
        </div>
        <a href="{{ route('admin.biens.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid #e5e7eb;border-radius:9px;font-size:12px;color:#6b7280;text-decoration:none">
            Formulaire classique
        </a>
    </div>

    {{-- ═══ BARRE DE PROGRESSION ═══ --}}
    <div class="wz-progress">
        <div class="wz-step active" id="prog-step-1">
            <div class="wz-step-bubble" id="bubble-1">1</div>
            <div class="wz-step-label">Informations<br>du bien</div>
        </div>
        <div class="wz-connector" id="conn-1"></div>
        <div class="wz-step" id="prog-step-2">
            <div class="wz-step-bubble" id="bubble-2">2</div>
            <div class="wz-step-label">Propriétaire</div>
        </div>
        <div class="wz-connector" id="conn-2"></div>
        <div class="wz-step" id="prog-step-3">
            <div class="wz-step-bubble" id="bubble-3">3</div>
            <div class="wz-step-label">Locataire<br><span style="font-weight:400;color:inherit">(optionnel)</span></div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.biens.store') }}" id="wz-form" novalidate>
        @csrf

        <div class="wz-layout">

            {{-- ═══ COLONNE PRINCIPALE ═══ --}}
            <div>

                {{-- ─────────────────────────────
                     ÉTAPE 1 — Informations du bien
                     ───────────────────────────── --}}
                <div class="wz-step-content active" id="step-1">

                    <div id="wz-alert-1" class="wz-alert error"></div>

                    <div class="card">
                        <div class="card-hd">
                            <div class="card-icon gold">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div>
                                <div class="card-title">Étape 1 — Caractéristiques du bien</div>
                                <div class="card-subtitle">Type, adresse et loyer</div>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="form-row" style="margin-bottom:14px">
                                <div class="form-group">
                                    <label class="form-label">Type de bien <span class="req">*</span></label>
                                    <select name="type" id="f-type" class="form-select" required>
                                        <option value="">— Sélectionner —</option>
                                        @foreach(\App\Models\Bien::TYPES as $key => $label)
                                            <option value="{{ $key }}" @selected(old('type') == $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Loyer mensuel (F CFA) <span class="req">*</span></label>
                                    <input type="number" name="loyer_mensuel" id="f-loyer" class="form-input"
                                           placeholder="ex: 250000" min="0" step="1000"
                                           value="{{ old('loyer_mensuel') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Adresse <span class="req">*</span></label>
                                <input type="text" name="adresse" id="f-adresse" class="form-input"
                                       placeholder="Rue, numéro, immeuble..." required
                                       value="{{ old('adresse') }}">
                            </div>

                            <div class="form-row" style="margin-bottom:14px">
                                <div class="form-group">
                                    <label class="form-label">Quartier <span class="opt">(optionnel)</span></label>
                                    <input type="text" name="quartier" class="form-input"
                                           placeholder="ex: Almadies" value="{{ old('quartier') }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Ville <span class="req">*</span></label>
                                    <input type="text" name="ville" id="f-ville" class="form-input"
                                           placeholder="ex: Dakar" required value="{{ old('ville') }}">
                                </div>
                            </div>

                            <div class="form-row" style="margin-bottom:14px">
                                <div class="form-group">
                                    <label class="form-label">Surface (m²) <span class="opt">(optionnel)</span></label>
                                    <input type="number" name="surface_m2" class="form-input"
                                           placeholder="ex: 120" min="1" value="{{ old('surface_m2') }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nombre de pièces <span class="opt">(optionnel)</span></label>
                                    <input type="number" name="nombre_pieces" class="form-input"
                                           placeholder="ex: 4" min="1" value="{{ old('nombre_pieces') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Taux de commission (%) <span class="opt">(optionnel)</span></label>
                                    <input type="number" name="taux_commission" class="form-input"
                                           placeholder="ex: 10" min="0" max="30" step="0.5"
                                           value="{{ old('taux_commission') }}">
                                </div>
                                <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:2px">
                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#374151">
                                        <input type="checkbox" name="meuble" value="1"
                                               @checked(old('meuble'))
                                               style="width:16px;height:16px;accent-color:#c9a84c">
                                        Bien meublé
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="wz-actions">
                            <a href="{{ route('admin.biens.index') }}" class="btn-prev">
                                Annuler
                            </a>
                            <button type="button" class="btn-next" onclick="goToStep(2)">
                                Étape suivante — Propriétaire
                                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </div>
                    </div>

                </div>

                {{-- ─────────────────────────────
                     ÉTAPE 2 — Lier un propriétaire
                     ───────────────────────────── --}}
                <div class="wz-step-content" id="step-2">

                    <div id="wz-alert-2" class="wz-alert error"></div>

                    <div class="card">
                        <div class="card-hd">
                            <div class="card-icon blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                            </div>
                            <div>
                                <div class="card-title">Étape 2 — Propriétaire du bien</div>
                                <div class="card-subtitle">Sélectionnez à qui appartient ce bien</div>
                            </div>
                        </div>
                        <div class="card-body">

                            @if($proprietaires->isEmpty())
                            <div style="padding:24px;text-align:center;background:#fafafa;border-radius:10px;border:1px dashed #e5e7eb">
                                <svg style="width:32px;height:32px;margin:0 auto 10px;display:block;opacity:.3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                <div style="font-size:13px;color:#374151;font-weight:600;margin-bottom:4px">Aucun propriétaire enregistré</div>
                                <div style="font-size:12px;color:#9ca3af;margin-bottom:14px">Créez d'abord un compte propriétaire</div>
                                <a href="{{ route('admin.users.proprietaires') }}" target="_blank"
                                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#c9a84c;color:#0d1117;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none">
                                    + Créer un propriétaire
                                </a>
                            </div>
                            @else
                            <div style="margin-bottom:12px">
                                <input type="text" placeholder="Rechercher un propriétaire..."
                                       oninput="filterPersons(this, 'prop-grid')"
                                       class="form-input" style="margin-bottom:0">
                            </div>
                            <div class="person-grid" id="prop-grid">
                                @foreach($proprietaires as $prop)
                                <label class="person-card" onclick="selectPerson(this, 'proprietaire_id', {{ $prop->id }}, '{{ addslashes($prop->name) }}', '{{ addslashes($prop->email) }}')">
                                    <input type="radio" name="proprietaire_id" value="{{ $prop->id }}" @checked(old('proprietaire_id') == $prop->id)>
                                    <div class="person-avatar" style="background:rgba(29,78,216,.1);color:#1d4ed8">
                                        {{ strtoupper(substr($prop->name, 0, 1)) }}
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <div class="person-name">{{ $prop->name }}</div>
                                        <div class="person-email">{{ $prop->email }}</div>
                                    </div>
                                    <div class="person-check">
                                        <svg style="width:10px;height:10px;color:#fff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @endif

                        </div>
                        <div class="wz-actions">
                            <button type="button" class="btn-prev" onclick="goToStep(1)">
                                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                                Retour
                            </button>
                            <button type="button" class="btn-next" onclick="goToStep(3)">
                                Étape suivante — Locataire
                                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </div>
                    </div>

                </div>

                {{-- ─────────────────────────────────
                     ÉTAPE 3 — Assigner un locataire
                     ─────────────────────────────── --}}
                <div class="wz-step-content" id="step-3">

                    <div class="card">
                        <div class="card-hd">
                            <div class="card-icon green">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div class="card-title">Étape 3 — Locataire actuel</div>
                                <div class="card-subtitle">Optionnel — vous pourrez l'assigner plus tard via un contrat</div>
                            </div>
                        </div>
                        <div class="card-body">

                            {{-- Option "Laisser vide" --}}
                            <div style="margin-bottom:14px;padding:12px 16px;background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb;display:flex;align-items:center;gap:10px">
                                <svg style="width:16px;height:16px;color:#9ca3af" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span style="font-size:12px;color:#6b7280">Ce champ est optionnel. Un bien sans locataire sera créé avec le statut <strong style="color:#6b7280">Disponible</strong>. Vous pourrez créer un contrat ultérieurement.</span>
                            </div>

                            @if($locataires->isEmpty())
                            <div style="padding:20px;text-align:center;background:#fafafa;border-radius:10px;border:1px dashed #e5e7eb">
                                <div style="font-size:12px;color:#9ca3af">Aucun locataire enregistré — le bien sera créé disponible.</div>
                            </div>
                            @else
                            <div style="margin-bottom:12px">
                                <input type="text" placeholder="Rechercher un locataire..."
                                       oninput="filterPersons(this, 'loc-grid')"
                                       class="form-input" style="margin-bottom:0">
                            </div>
                            <div class="person-grid" id="loc-grid">

                                {{-- Option "Aucun locataire" --}}
                                <label class="person-card skip-option selected" onclick="selectPerson(this, 'locataire_id_wizard', null, 'Aucun', '')">
                                    <input type="radio" name="locataire_id_wizard" value="" checked>
                                    <div class="person-avatar" style="background:#f3f4f6;color:#9ca3af">
                                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                    </div>
                                    <div>
                                        <div class="person-name" style="color:#6b7280">Aucun locataire</div>
                                        <div class="person-email">Bien disponible</div>
                                    </div>
                                    <div class="person-check">
                                        <svg style="width:10px;height:10px;color:#fff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </label>

                                @foreach($locataires as $loc)
                                <label class="person-card" onclick="selectPerson(this, 'locataire_id_wizard', {{ $loc->id }}, '{{ addslashes($loc->name) }}', '{{ addslashes($loc->telephone ?? $loc->email) }}')">
                                    <input type="radio" name="locataire_id_wizard" value="{{ $loc->id }}">
                                    <div class="person-avatar" style="background:rgba(22,163,74,.1);color:#16a34a">
                                        {{ strtoupper(substr($loc->name, 0, 1)) }}
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <div class="person-name">{{ $loc->name }}</div>
                                        <div class="person-email">{{ $loc->telephone ?? $loc->email }}</div>
                                    </div>
                                    <div class="person-check">
                                        <svg style="width:10px;height:10px;color:#fff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @endif

                        </div>
                        <div class="wz-actions">
                            <button type="button" class="btn-prev" onclick="goToStep(2)">
                                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                                Retour
                            </button>
                            <button type="submit" class="btn-submit-final">
                                <svg style="width:15px;height:15px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Créer le bien
                            </button>
                        </div>
                    </div>

                </div>

            </div>

            {{-- ═══ PANNEAU RÉCAP (sticky) ═══ --}}
            <div>
                <div class="recap-panel">
                    <div class="recap-hd">
                        <div class="recap-title">Récapitulatif</div>
                        <div class="recap-step-label" id="recap-step-label">Étape 1 sur 3</div>
                    </div>
                    <div class="recap-body">
                        <div class="recap-section">
                            <div class="recap-section-title">Bien</div>
                            <div class="rr">
                                <span class="rr-lbl">Type</span>
                                <span class="rr-val" id="recap-type"><em class="rr-val empty">—</em></span>
                            </div>
                            <div class="rr">
                                <span class="rr-lbl">Adresse</span>
                                <span class="rr-val" id="recap-adresse"><em class="rr-val empty">—</em></span>
                            </div>
                            <div class="rr">
                                <span class="rr-lbl">Ville</span>
                                <span class="rr-val" id="recap-ville"><em class="rr-val empty">—</em></span>
                            </div>
                            <div class="rr">
                                <span class="rr-lbl">Loyer</span>
                                <span class="rr-val gold" id="recap-loyer"><em class="rr-val empty">—</em></span>
                            </div>
                        </div>
                        <div class="recap-section">
                            <div class="recap-section-title">Propriétaire</div>
                            <div class="rr">
                                <span class="rr-lbl">Nom</span>
                                <span class="rr-val" id="recap-prop"><em class="rr-val empty">Non sélectionné</em></span>
                            </div>
                        </div>
                        <div class="recap-section">
                            <div class="recap-section-title">Locataire</div>
                            <div class="rr">
                                <span class="rr-lbl">Nom</span>
                                <span class="rr-val green" id="recap-loc"><em class="rr-val empty">Optionnel</em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>

</div>

<script>
let currentStep = 1;
const totalSteps = 3;

// ── Navigation entre étapes ──────────────────
function goToStep(step) {
    if (step > currentStep && !validateStep(currentStep)) return;

    // Masquer étape courante
    document.getElementById('step-' + currentStep).classList.remove('active');
    document.getElementById('prog-step-' + currentStep).classList.remove('active');
    document.getElementById('prog-step-' + currentStep).classList.add('done');
    document.getElementById('bubble-' + currentStep).innerHTML =
        '<svg style="width:16px;height:16px;color:#fff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';

    if (step < currentStep) {
        // Retour en arrière — undone
        for (let i = step; i <= currentStep; i++) {
            document.getElementById('prog-step-' + i).classList.remove('done', 'active');
            document.getElementById('bubble-' + i).textContent = i;
        }
        if (step > 1) {
            document.getElementById('conn-' + (step - 1)).classList.add('done');
        }
    }

    // Activer nouveau step
    currentStep = step;
    document.getElementById('step-' + step).classList.add('active');
    document.getElementById('prog-step-' + step).classList.add('active');
    document.getElementById('prog-step-' + step).classList.remove('done');

    // Connecteur
    if (step > 1) document.getElementById('conn-' + (step - 1)).classList.add('done');

    // Label récap
    document.getElementById('recap-step-label').textContent = 'Étape ' + step + ' sur ' + totalSteps;

    // Scroll top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Validation par étape ─────────────────────
function validateStep(step) {
    const alert = document.getElementById('wz-alert-' + step);
    if (!alert) return true;

    alert.style.display = 'none';

    if (step === 1) {
        const type   = document.getElementById('f-type').value;
        const adresse = document.getElementById('f-adresse').value.trim();
        const ville  = document.getElementById('f-ville').value.trim();
        const loyer  = document.getElementById('f-loyer').value;
        const errors = [];
        if (!type)   errors.push('le type de bien');
        if (!adresse) errors.push("l'adresse");
        if (!ville)  errors.push('la ville');
        if (!loyer)  errors.push('le loyer mensuel');
        if (errors.length) {
            alert.style.display = 'flex';
            alert.innerHTML = '<svg style="width:14px;height:14px;flex-shrink:0;margin-top:1px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Veuillez renseigner : ' + errors.join(', ') + '.';
            return false;
        }
    }

    if (step === 2) {
        const prop = document.querySelector('input[name="proprietaire_id"]:checked');
        if (!prop || !prop.value) {
            alert.style.display = 'flex';
            alert.innerHTML = '<svg style="width:14px;height:14px;flex-shrink:0;margin-top:1px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Veuillez sélectionner un propriétaire.';
            return false;
        }
    }

    return true;
}

// ── Sélection d'une personne (radio card) ────
function selectPerson(card, fieldName, id, name, sub) {
    const grid = card.closest('.person-grid');
    grid.querySelectorAll('.person-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    card.querySelector('input[type=radio]').checked = true;

    // Mettre à jour récap
    if (fieldName === 'proprietaire_id') {
        const el = document.getElementById('recap-prop');
        el.textContent = name;
        el.className = 'rr-val';
    }
    if (fieldName === 'locataire_id_wizard') {
        const el = document.getElementById('recap-loc');
        if (id) {
            el.textContent = name;
            el.className = 'rr-val green';
        } else {
            el.innerHTML = '<em class="rr-val empty">Disponible (sans locataire)</em>';
        }
    }
}

// ── Mise à jour récap en temps réel ──────────
function updateRecap() {
    const typeEl = document.getElementById('f-type');
    const typeText = typeEl.options[typeEl.selectedIndex]?.text;
    const adresse = document.getElementById('f-adresse').value.trim();
    const ville   = document.getElementById('f-ville').value.trim();
    const loyer   = document.getElementById('f-loyer').value;

    const recapType   = document.getElementById('recap-type');
    const recapAdresse = document.getElementById('recap-adresse');
    const recapVille  = document.getElementById('recap-ville');
    const recapLoyer  = document.getElementById('recap-loyer');

    recapType.textContent   = typeText && typeEl.value ? typeText : '';
    recapAdresse.textContent = adresse || '';
    recapVille.textContent  = ville || '';
    recapLoyer.textContent  = loyer ? parseInt(loyer).toLocaleString('fr-FR') + ' F' : '';

    if (!recapType.textContent)   recapType.innerHTML = '<em class="rr-val empty">—</em>';
    if (!recapAdresse.textContent) recapAdresse.innerHTML = '<em class="rr-val empty">—</em>';
    if (!recapVille.textContent)  recapVille.innerHTML = '<em class="rr-val empty">—</em>';
    if (!recapLoyer.textContent)  recapLoyer.innerHTML = '<em class="rr-val empty">—</em>';
}

// ── Filtre recherche ─────────────────────────
function filterPersons(input, gridId) {
    const q = input.value.toLowerCase();
    document.querySelectorAll('#' + gridId + ' .person-card').forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(q) ? '' : 'none';
    });
}

// ── Listeners ───────────────────────────────
document.getElementById('f-type').addEventListener('change', updateRecap);
document.getElementById('f-adresse').addEventListener('input', updateRecap);
document.getElementById('f-ville').addEventListener('input', updateRecap);
document.getElementById('f-loyer').addEventListener('input', updateRecap);

// Pré-sélectionner si old() en cas d'erreur
@if(old('proprietaire_id'))
document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="proprietaire_id"]:checked');
    if (checked) checked.closest('.person-card')?.classList.add('selected');
});
@endif
</script>
@endsection
