@extends('layouts.app')
@section('title', 'Nouveau contrat')
@section('breadcrumb', 'Contrats › Nouveau')

@section('content')
<style>
.form-grid { display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:10px; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.form-row-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px; }
.form-hint { font-size:11px;color:#9ca3af;margin-top:3px; }
.recap-card { background:#0d1117;border-radius:14px;overflow:hidden;position:sticky;top:24px; }
.recap-hd { padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.07); }
.recap-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#fff; }
.recap-body { padding:16px 18px; }
.rp-row { display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.06); }
.rp-row:last-child { border-bottom:none; }
.rp-lbl { font-size:12px;color:rgba(255,255,255,.4); }
.rp-val { font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#fff; }
.rp-val.gold { color:#c9a84c; }
.rp-val.green { color:#4ade80; }
.rp-sep { height:1px;background:rgba(255,255,255,.07);margin:10px 0; }
.rp-total { background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:9px;padding:12px 14px;margin-top:12px; }
.rp-total-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.rp-total-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#c9a84c; }
.btn-new-loc { display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#2a4a7f;background:#dbeafe;padding:4px 10px;border-radius:6px;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;transition:all .15s; }
.btn-new-loc:hover { background:#bfdbfe; }
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff;border-radius:14px;padding:24px;width:420px;max-width:90vw; }
.modal-title { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#0d1117;margin-bottom:16px; }
.btn-gold { padding:8px 18px;border-radius:8px;border:none;background:#c9a84c;color:#0d1117;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center;gap:6px; }
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:18px">
        <a href="{{ route('admin.contrats.index') }}" style="color:#6b7280;text-decoration:none">Contrats</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Nouveau contrat</span>
    </div>

    <div style="margin-bottom:20px">
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Créer un contrat de bail</h1>
        <p style="font-size:13px;color:#6b7280;margin-top:3px">Le loyer contractuel = loyer nu + charges + TOM.</p>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #dc2626;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#dc2626">
        <strong>Veuillez corriger les erreurs :</strong>
        <ul style="margin-top:4px;padding-left:16px">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.contrats.store') }}" id="form-contrat">
        @csrf
        <div class="form-grid">

            {{-- ═══ COLONNE GAUCHE ═══ --}}
            <div>

                {{-- BIEN & LOCATAIRE --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="card-title">Bien & Locataire</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Bien à louer <span class="req">*</span></label>
                            <select name="bien_id" id="bien_id"
                                    class="form-select {{ $errors->has('bien_id') ? 'error':'' }}"
                                    onchange="chargerInfosBien(this)">
                                <option value="">— Sélectionner un bien disponible —</option>
                                @foreach($biens as $bien)
                                    <option value="{{ $bien->id }}"
                                        data-loyer="{{ $bien->loyer_mensuel }}"
                                        data-commission="{{ $bien->taux_commission }}"
                                        data-proprio="{{ $bien->proprietaire?->name }}"
                                        data-meuble="{{ $bien->meuble ? '1':'0' }}"
                                        data-type="{{ $bien->type }}"
                                        {{ old('bien_id', $bienPreselectionne?->id) == $bien->id ? 'selected':'' }}>
                                        {{ $bien->reference }} — {{ \App\Models\Bien::TYPES[$bien->type] ?? $bien->type }} — {{ $bien->adresse }}, {{ $bien->ville }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bien_id')<div class="form-error">{{ $message }}</div>@enderror
                            <div id="info-proprio" style="display:none;margin-top:6px;padding:8px 10px;background:#f9fafb;border-radius:7px;font-size:11px;color:#6b7280"></div>
                        </div>

                        <div class="form-group">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px">
                                <label class="form-label" style="margin-bottom:0">Locataire <span class="req">*</span></label>
                                <button type="button" class="btn-new-loc" onclick="ouvrirModalLocataire()">
                                    <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Nouveau locataire
                                </button>
                            </div>
                            <select name="locataire_id" id="locataire_id"
                                    class="form-select {{ $errors->has('locataire_id') ? 'error':'' }}">
                                <option value="">— Sélectionner un locataire —</option>
                                @foreach($locataires as $loc)
                                    <option value="{{ $loc->id }}" {{ old('locataire_id') == $loc->id ? 'selected':'' }}>
                                        {{ $loc->name }} — {{ $loc->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('locataire_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- DURÉE ET TYPE --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="card-title">Durée & Type de bail</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Date de début <span class="req">*</span></label>
                                <input type="date" name="date_debut" id="date_debut"
                                       class="form-input {{ $errors->has('date_debut') ? 'error':'' }}"
                                       value="{{ old('date_debut', now()->format('Y-m-d')) }}">
                                @error('date_debut')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date de fin <span class="opt">(optionnel)</span></label>
                                <input type="date" name="date_fin"
                                       class="form-input" value="{{ old('date_fin') }}">
                                <div class="form-hint">Laisser vide = contrat ouvert</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type de bail <span class="req">*</span></label>
                            <select name="type_bail" id="type_bail"
                                    class="form-select {{ $errors->has('type_bail') ? 'error':'' }}"
                                    onchange="mettreAJourRecap()">
                                @foreach($typesBail as $val => $label)
                                    <option value="{{ $val }}" {{ old('type_bail', 'habitation') === $val ? 'selected':'' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type_bail')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Référence bail <span class="opt">(optionnel)</span></label>
                            <input type="text" name="reference_bail"
                                   class="form-input" value="{{ old('reference_bail') }}"
                                   placeholder="Ex: BAIL-2024-001 (générée auto si vide)">
                        </div>
                    </div>
                </div>

                {{-- LOYER --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="card-title">Ventilation du loyer</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Loyer nu (FCFA) <span class="req">*</span></label>
                                <input type="number" name="loyer_nu" id="loyer_nu"
                                       class="form-input {{ $errors->has('loyer_nu') ? 'error':'' }}"
                                       value="{{ old('loyer_nu', $bienPreselectionne?->loyer_mensuel) }}"
                                       min="0" step="500" oninput="mettreAJourRecap()">
                                <div class="form-hint">Hors charges et TOM</div>
                                @error('loyer_nu')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Charges mensuelles <span class="opt">(optionnel)</span></label>
                                <input type="number" name="charges_mensuelles" id="charges_mensuelles"
                                       class="form-input" value="{{ old('charges_mensuelles', 0) }}"
                                       min="0" step="500" oninput="mettreAJourRecap()">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">TOM <span class="opt">(Taxe ordures ménagères)</span></label>
                                <input type="number" name="tom_amount" id="tom_amount"
                                       class="form-input" value="{{ old('tom_amount', 0) }}"
                                       min="0" step="100" oninput="mettreAJourRecap()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Indexation annuelle (%)</label>
                                <input type="number" name="indexation_annuelle"
                                       class="form-input" value="{{ old('indexation_annuelle', 0) }}"
                                       min="0" max="20" step="0.5">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CAUTION & FRAIS --}}
                @php $avecCaution = old('avec_caution', '1') === '1'; @endphp
                <div class="card">
                    <div class="card-hd">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="card-icon green">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <div class="card-title">Caution & Frais</div>
                        </div>
                        {{-- Toggle caution --}}
                        <div style="display:flex;align-items:center;gap:6px;background:#f3f4f6;border-radius:8px;padding:3px">
                            <button type="button" id="btn-avec-caution"
                                onclick="toggleCaution(true)"
                                style="padding:5px 14px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:all .15s;
                                       background:{{ $avecCaution ? '#0d1117' : 'transparent' }};
                                       color:{{ $avecCaution ? '#fff' : '#6b7280' }}">
                                Avec caution
                            </button>
                            <button type="button" id="btn-sans-caution"
                                onclick="toggleCaution(false)"
                                style="padding:5px 14px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:all .15s;
                                       background:{{ !$avecCaution ? '#0d1117' : 'transparent' }};
                                       color:{{ !$avecCaution ? '#fff' : '#6b7280' }}">
                                Sans caution
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="avec_caution" id="avec_caution" value="{{ old('avec_caution', '1') }}">
                    <div class="card-body">
                        <div id="bloc-caution" style="{{ $avecCaution ? '' : 'display:none' }}">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Caution (FCFA)</label>
                                <input type="number" name="caution" id="caution"
                                       class="form-input {{ $errors->has('caution') ? 'error':'' }}"
                                       value="{{ old('caution') }}" min="0" step="500">
                                @error('caution')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nombre de mois de caution</label>
                                <select name="nombre_mois_caution" id="nombre_mois_caution"
                                        class="form-select" onchange="calcCaution()">
                                    @foreach([1,2,3,6] as $n)
                                        <option value="{{ $n }}" {{ old('nombre_mois_caution', 1) == $n ? 'selected':'' }}>
                                            {{ $n }} mois
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Frais d'agence HT (FCFA) <span class="opt">(optionnel)</span></label>
                            <input type="number" name="frais_agence"
                                   class="form-input" value="{{ old('frais_agence', 0) }}" min="0" step="500">
                            <div class="form-hint">Honoraires HT perçus à la signature · TVA 18% ajoutée auto · Standard : 1 mois de loyer nu</div>
                        </div>
                    </div>
                </div>

                {{-- PARAMÈTRES FISCAUX --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon" style="background:#fef3c7;color:#d97706">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="card-title">Paramètres fiscaux</div>
                    </div>
                    <div class="card-body">
                        @include('admin.contrats._section-fiscal', ['contrat' => null])
                    </div>
                </div>

                {{-- GARANT --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <div class="card-title">Garant <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nom du garant</label>
                                <input type="text" name="garant_nom" class="form-input"
                                       value="{{ old('garant_nom') }}" placeholder="Prénom Nom">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="garant_telephone" class="form-input"
                                       value="{{ old('garant_telephone') }}" placeholder="+221 7X XXX XX XX">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Adresse</label>
                                <input type="text" name="garant_adresse" class="form-input"
                                       value="{{ old('garant_adresse') }}" placeholder="Adresse complète">
                            </div>
                            <div class="form-group">
                                <label class="form-label">CNI / Pièce d'identité</label>
                                <input type="text" name="garant_cni" class="form-input"
                                       value="{{ old('garant_cni') }}" placeholder="N° CNI ou passeport">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- OBSERVATIONS --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="card-title">Observations <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <textarea name="observations" class="form-textarea"
                            rows="3"
                            placeholder="État des lieux, observations générales…">{{ old('observations') }}</textarea>

                        <div class="form-group" style="margin-top:14px">
                            <label class="form-label">
                                Clauses particulières
                                <span class="opt">(spécifiques à ce bail)</span>
                            </label>
                            <textarea name="clauses_particulieres" class="form-textarea"
                                rows="5"
                                placeholder="Ex : interdiction d'animaux, travaux autorisés, conditions de résiliation anticipée…&#10;&#10;Votre modèle de clauses agence sera automatiquement ajouté dans le bail PDF.">{{ old('clauses_particulieres') }}</textarea>
                            @if(auth()->user()->agency?->modele_contrat)
                            <div style="font-size:11px;color:#16a34a;margin-top:4px;display:flex;align-items:center;gap:4px">
                                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                Modèle de clauses agence configuré — inclus automatiquement dans le bail PDF
                            </div>
                            @else
                            <div style="font-size:11px;color:#9ca3af;margin-top:4px;display:flex;align-items:center;gap:4px">
                                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <a href="{{ route('admin.agency.settings') }}" style="color:#c9a84c;text-decoration:none">Configurer votre modèle de clauses agence →</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="submit-bar">
                        <a href="{{ route('admin.contrats.index') }}" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="20 6 9 17 4 12"/></svg>
                            Créer le contrat
                        </button>
                    </div>
                </div>

            </div>{{-- fin colonne gauche --}}

            {{-- ═══ COLONNE DROITE : RÉCAPITULATIF ═══ --}}
            <div>
                <div class="recap-card">
                    <div class="recap-hd"><div class="recap-title">Récapitulatif</div></div>
                    <div class="recap-body">

                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.25);margin-bottom:8px">Loyer</div>
                        <div class="rp-row">
                            <div class="rp-lbl">Loyer nu</div>
                            <div class="rp-val" id="rp-loyer-nu">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">+ Charges HT</div>
                            <div class="rp-val" id="rp-charges">— F</div>
                        </div>
                        <div class="rp-row" id="row-tva-charges" style="display:none">
                            <div class="rp-lbl">+ TVA charges 18% <span style="font-size:10px;opacity:.6">(forfait)</span></div>
                            <div class="rp-val" style="color:#f59e0b" id="rp-tva-charges">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">+ TOM</div>
                            <div class="rp-val" id="rp-tom">— F</div>
                        </div>
                        <div class="rp-row" id="row-tva-loyer" style="display:none">
                            <div class="rp-lbl">+ TVA loyer 18% <span style="font-size:10px;opacity:.6">(Art.354)</span></div>
                            <div class="rp-val" style="color:#f59e0b" id="rp-tva-loyer">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl" style="font-weight:600;color:rgba(255,255,255,.7)">= Total encaissé</div>
                            <div class="rp-val gold" id="rp-loyer-total">— F</div>
                        </div>

                        <div class="rp-sep"></div>
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.25);margin-bottom:8px">Commission</div>
                        <div class="rp-row">
                            <div class="rp-lbl">Commission HT</div>
                            <div class="rp-val gold" id="rp-comm-ht">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">TVA 18% (Art.357)</div>
                            <div class="rp-val" id="rp-tva-comm">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">Commission TTC</div>
                            <div class="rp-val gold" id="rp-comm-ttc">— F</div>
                        </div>

                        <div class="rp-row" id="row-brs" style="display:none">
                            <div class="rp-lbl">− BRS 15% <span style="font-size:10px;opacity:.6">(Art.156)</span></div>
                            <div class="rp-val" style="color:#f87171" id="rp-brs">— F</div>
                        </div>
                        <div class="rp-total">
                            <div class="rp-total-lbl">Net à verser propriétaire</div>
                            <div class="rp-total-val" id="rp-net">— FCFA</div>
                        </div>

                        <div style="margin-top:14px;padding-top:12px;border-top:1px solid rgba(255,255,255,.07)">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.25);margin-bottom:8px">À la signature</div>
                            <div class="rp-row">
                                <div class="rp-lbl">Caution</div>
                                <div class="rp-val" id="rp-caution">— F</div>
                            </div>
                            <div class="rp-row">
                                <div class="rp-lbl">Taux commission</div>
                                <div class="rp-val" id="rp-taux-comm">— %</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

{{-- MODAL NOUVEAU LOCATAIRE --}}
<div class="modal-overlay" id="modal-locataire">
    <div class="modal-box">
        <div class="modal-title">Créer un nouveau locataire</div>
        <div id="modal-error" style="display:none;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#dc2626"></div>
        <div class="form-group">
            <label class="form-label">Nom complet <span class="req">*</span></label>
            <input type="text" id="loc-name" class="form-input" placeholder="Prénom Nom">
        </div>
        <div class="form-group">
            <label class="form-label">Email <span class="req">*</span></label>
            <input type="email" id="loc-email" class="form-input" placeholder="email@exemple.com">
        </div>
        <div class="form-group">
            <label class="form-label">Téléphone</label>
            <input type="text" id="loc-tel" class="form-input" placeholder="+221 7X XXX XX XX">
        </div>
        <div class="form-group">
            <label class="form-label">Mot de passe <span class="req">*</span></label>
            <input type="password" id="loc-pwd" class="form-input" placeholder="Min. 8 caractères">
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px">
            <button type="button" class="btn-cancel" onclick="fermerModalLocataire()">Annuler</button>
            <button type="button" class="btn-gold" onclick="creerLocataire()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="20 6 9 17 4 12"/></svg>
                Créer et sélectionner
            </button>
        </div>
    </div>
</div>

<script>
// ── Toggle caution ─────────────────────────────────────────────────────────
function toggleCaution(avec) {
    document.getElementById('avec_caution').value = avec ? '1' : '0';
    document.getElementById('bloc-caution').style.display = avec ? '' : 'none';

    const btnAvec  = document.getElementById('btn-avec-caution');
    const btnSans  = document.getElementById('btn-sans-caution');
    btnAvec.style.background = avec  ? '#0d1117' : 'transparent';
    btnAvec.style.color      = avec  ? '#fff'    : '#6b7280';
    btnSans.style.background = !avec ? '#0d1117' : 'transparent';
    btnSans.style.color      = !avec ? '#fff'    : '#6b7280';

    // Vider le champ caution si désactivé
    if (!avec) {
        document.getElementById('caution').value = '';
    }
}

// ── Données biens passées depuis PHP ───────────────────────────────────────
const biensData = @json($biens->keyBy('id'));

function fmt(n) { return Math.round(n).toLocaleString('fr-FR') + ' F'; }

// ── Chargement infos bien ──────────────────────────────────────────────────
function chargerInfosBien(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt.value) {
        document.getElementById('info-proprio').style.display = 'none';
        mettreAJourRecap();
        return;
    }

    const loyer      = parseFloat(opt.dataset.loyer)      || 0;
    const commission = parseFloat(opt.dataset.commission)  || 10;
    const proprio    = opt.dataset.proprio                 || '—';
    const meuble     = opt.dataset.meuble === '1';
    const type       = opt.dataset.type;

    // Pré-remplir loyer nu
    document.getElementById('loyer_nu').value = loyer;

    // Suggérer type bail selon meuble/type
    const typeBailSelect = document.getElementById('type_bail');
    if (type === 'bureau' || type === 'commerce') {
        typeBailSelect.value = 'commercial';
    } else if (meuble) {
        typeBailSelect.value = 'habitation';
    }

    // Calculer caution auto (1 mois par défaut)
    const nbMois = parseInt(document.getElementById('nombre_mois_caution').value) || 1;
    document.getElementById('caution').value = Math.round(loyer * nbMois);

    // Synchroniser la case TVA loyer selon le type de bail + meublé (CGI SN art. 355)
    // habitation meublée, commercial, mixte, saisonnier meublé → TVA applicable
    const typeBailVal = typeBailSelect.value;
    const tvaCheckbox = document.getElementById('loyer_assujetti_tva');
    if (tvaCheckbox) {
        const doitEtreTva = (typeBailVal === 'commercial' || typeBailVal === 'mixte')
            || (meuble && (typeBailVal === 'habitation' || typeBailVal === 'saisonnier'));
        tvaCheckbox.checked = doitEtreTva;
        updateFiscalBadge();
    }

    // Info proprio
    const infoProprio = document.getElementById('info-proprio');
    infoProprio.style.display = 'block';
    infoProprio.innerHTML = `<strong>Propriétaire :</strong> ${proprio} · <strong>Commission :</strong> ${commission}%`;

    mettreAJourRecap();
}

// ── Calcul caution auto ────────────────────────────────────────────────────
function calcCaution() {
    const loyer  = parseFloat(document.getElementById('loyer_nu').value) || 0;
    const nbMois = parseInt(document.getElementById('nombre_mois_caution').value) || 1;
    document.getElementById('caution').value = Math.round(loyer * nbMois);
    mettreAJourRecap();
}

// ── Mise à jour récapitulatif ──────────────────────────────────────────────
function mettreAJourRecap() {
    const loyerNu    = parseFloat(document.getElementById('loyer_nu').value)           || 0;
    const charges    = parseFloat(document.getElementById('charges_mensuelles').value) || 0;
    const tom        = parseFloat(document.getElementById('tom_amount').value)          || 0;
    const caution    = parseFloat(document.getElementById('caution').value)             || 0;
    const tvaChecked     = document.getElementById('loyer_assujetti_tva')?.checked ?? false;
    const brsChecked     = document.getElementById('brs_applicable')?.checked ?? false;
    const chargesForait  = document.getElementById('charges_assujetties_tva')?.checked ?? false;

    const bienSelect = document.getElementById('bien_id');
    const opt        = bienSelect.options[bienSelect.selectedIndex];
    const tauxComm   = parseFloat(opt?.dataset?.commission || 10);

    // TVA loyer — Art. 354 CGI SN : assiette = loyer + TOM
    const tvaLoyer   = tvaChecked ? Math.round((loyerNu + tom) * 0.18) : 0;
    const loyerTtc   = loyerNu + tvaLoyer;

    // TVA charges — DGI SN : forfait = prestation de service assujettie (même taux que loyer)
    const tvaCharges = chargesForait ? Math.round(charges * (tvaChecked ? 0.18 : 0)) : 0;
    const chargesTtc = charges + tvaCharges;

    const montantEncaisse = loyerTtc + chargesTtc + tom;

    // Commission — uniquement sur loyer HT (jamais TVA/TOM/charges)
    const commHt     = Math.round(loyerNu * tauxComm / 100);
    const tvaComm    = Math.round(commHt * 0.18);
    const commTtc    = commHt + tvaComm;
    const netProprio = montantEncaisse - commTtc;

    // BRS — Art. 196bis CGI SN : assiette = loyer TTC + TOM
    const brsAmount  = brsChecked ? Math.round((loyerTtc + tom) * 0.15) : 0;
    const netAVerser = netProprio - brsAmount;

    document.getElementById('rp-loyer-nu').textContent    = fmt(loyerNu);
    document.getElementById('rp-charges').textContent     = fmt(charges);

    const rowTvaCharges = document.getElementById('row-tva-charges');
    if (rowTvaCharges) rowTvaCharges.style.display = (chargesForait && tvaCharges > 0) ? '' : 'none';
    const elTvaCharges = document.getElementById('rp-tva-charges');
    if (elTvaCharges) elTvaCharges.textContent = fmt(tvaCharges);

    document.getElementById('rp-tom').textContent         = fmt(tom);

    const rowTva = document.getElementById('row-tva-loyer');
    if (rowTva) rowTva.style.display = tvaChecked ? '' : 'none';
    const elTvaLoyer = document.getElementById('rp-tva-loyer');
    if (elTvaLoyer) elTvaLoyer.textContent = fmt(tvaLoyer);

    document.getElementById('rp-loyer-total').textContent = fmt(montantEncaisse);
    document.getElementById('rp-comm-ht').textContent     = fmt(commHt);
    document.getElementById('rp-tva-comm').textContent    = fmt(tvaComm);
    document.getElementById('rp-comm-ttc').textContent    = fmt(commTtc);

    const rowBrs = document.getElementById('row-brs');
    if (rowBrs) rowBrs.style.display = brsChecked ? '' : 'none';
    const elBrs = document.getElementById('rp-brs');
    if (elBrs) elBrs.textContent = fmt(brsAmount);

    document.getElementById('rp-net').textContent         = fmt(netAVerser) + ' CFA';
    document.getElementById('rp-caution').textContent     = fmt(caution);
    document.getElementById('rp-taux-comm').textContent   = tauxComm + ' %';

    if (typeof verifierAlerteBrsCommercial === 'function') verifierAlerteBrsCommercial();
}

// ── Modal nouveau locataire ────────────────────────────────────────────────
function ouvrirModalLocataire() {
    document.getElementById('modal-locataire').classList.add('open');
    document.getElementById('loc-name').focus();
}

function fermerModalLocataire() {
    document.getElementById('modal-locataire').classList.remove('open');
    document.getElementById('modal-error').style.display = 'none';
    ['loc-name','loc-email','loc-tel','loc-pwd'].forEach(id => document.getElementById(id).value = '');
}

async function creerLocataire() {
    const name = document.getElementById('loc-name').value.trim();
    const email= document.getElementById('loc-email').value.trim();
    const tel  = document.getElementById('loc-tel').value.trim();
    const pwd  = document.getElementById('loc-pwd').value;

    const errDiv = document.getElementById('modal-error');
    errDiv.style.display = 'none';

    if (!name || !email || !pwd) {
        errDiv.style.display = 'block';
        errDiv.textContent = 'Nom, email et mot de passe sont obligatoires.';
        return;
    }

    try {
        const response = await fetch('{{ route('admin.contrats.locataire-rapide') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ name, email, telephone: tel, password: pwd }),
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            errDiv.style.display = 'block';
            errDiv.textContent = data.message || 'Erreur lors de la création.';
            return;
        }

        // Ajouter l'option dans le select et la sélectionner
        const select = document.getElementById('locataire_id');
        const option = new Option(`${data.name} — ${email}`, data.id, true, true);
        select.add(option);
        select.value = data.id;

        fermerModalLocataire();

    } catch(e) {
        errDiv.style.display = 'block';
        errDiv.textContent = 'Erreur réseau. Veuillez réessayer.';
    }
}

// Fermer modal en cliquant l'overlay
document.getElementById('modal-locataire').addEventListener('click', function(e) {
    if (e.target === this) fermerModalLocataire();
});

// Init
mettreAJourRecap();

// Si bien présélectionné
@if($bienPreselectionne)
    document.getElementById('bien_id').dispatchEvent(new Event('change'));
    chargerInfosBien(document.getElementById('bien_id'));
@endif
</script>
@endsection