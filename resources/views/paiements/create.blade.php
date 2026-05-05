@extends('layouts.app')
@section('title', 'Enregistrer un paiement')
@section('breadcrumb', 'Paiements › Nouveau')

@section('content')
<style>
.form-grid { display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start; }
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
.form-hint { font-size:11px;color:#9ca3af;margin-top:3px; }
.form-textarea { resize:vertical;min-height:70px; }

/* Recap */
.recap-card { background:#0d1117;border-radius:14px;overflow:hidden;position:sticky;top:24px; }
.recap-hd { padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.07); }
.recap-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#fff; }
.recap-body { padding:16px 18px; }
.rp-row { display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.06); }
.rp-row:last-child { border-bottom:none; }
.rp-lbl { font-size:12px;color:rgba(255,255,255,.4); }
.rp-val { font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#fff; }
.rp-val.gold  { color:#c9a84c; }
.rp-val.green { color:#4ade80; }
.rp-val.red   { color:#f87171; }
.rp-sep { height:1px;background:rgba(255,255,255,.07);margin:10px 0; }
.rp-total { background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:9px;padding:12px 14px;margin-top:12px; }
.rp-total-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.rp-total-val { font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#c9a84c; }

/* Contrat sélectionné */
.contrat-info { background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:9px;padding:12px 14px;margin-top:10px; }
.contrat-info-row { display:flex;justify-content:space-between;font-size:12px;padding:3px 0; }
.ci-lbl { color:rgba(255,255,255,.4); }
.ci-val { color:#e6edf3;font-weight:500; }

.submit-bar { display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-cancel { padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center; }
.btn-submit { padding:8px 18px;border-radius:8px;border:none;background:#2a4a7f;color:#fff;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center;gap:6px; }

/* Badge montant correct */
.montant-ok   { color:#16a34a;font-size:11px;margin-top:3px;display:none; }
.montant-diff { color:#d97706;font-size:11px;margin-top:3px;display:none; }

/* Badge BRS */
.brs-badge {
    display:none;
    margin-top:10px;
    background:#fef2f2;
    border:1px solid #fecaca;
    border-left:4px solid #dc2626;
    border-radius:0 8px 8px 0;
    padding:10px 14px;
    font-size:12px;
    color:#991b1b;
}
.brs-badge-title { font-weight:700; font-size:12px; margin-bottom:4px; color:#dc2626; }
.brs-badge-body  { color:#7f1d1d; line-height:1.6; }
.brs-badge-link  { color:#dc2626; font-weight:600; text-decoration:underline; font-size:11px; }
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:18px">
        <a href="{{ route('admin.paiements.index') }}" style="color:#6b7280;text-decoration:none">Paiements</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Enregistrer un paiement</span>
    </div>

    <div style="margin-bottom:20px">
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
            Enregistrer un paiement
        </h1>
        <p style="font-size:13px;color:#6b7280;margin-top:3px">
            La commission et le net propriétaire sont calculés automatiquement.
        </p>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #dc2626;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#dc2626">
        <ul style="padding-left:16px;margin:0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.paiements.store') }}" id="form-paiement">
        @csrf
        <div class="form-grid">

            {{-- ═══ COLONNE GAUCHE ═══ --}}
            <div>

                {{-- CONTRAT --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="card-title">Contrat</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Contrat concerné <span class="req">*</span></label>
                            <select name="contrat_id" id="contrat_id"
                                    class="form-select {{ $errors->has('contrat_id') ? 'error':'' }}"
                                    onchange="chargerContrat(this.value)">
                                <option value="">— Sélectionner un contrat actif —</option>
                                @foreach($contrats as $c)
                                    @php
                                        $locProfile  = $c->locataire?->locataire;
                                        $estEntreprise = (bool) ($locProfile?->est_entreprise
                                            ?? in_array($c->type_bail ?? '', ['commercial', 'mixte']));
                                        $tauxBrs = $c->taux_brs_manuel
                                            ?? $locProfile?->taux_brs_override
                                            ?? 15;
                                        $brsApplicable = $estEntreprise ? 1 : 0;
                                        $loyerAssujetti = $c->loyer_assujetti_tva
                                            ?? in_array($c->type_bail ?? '', ['commercial', 'mixte'])
                                            ? 1 : 0;
                                        $tauxTva = $c->taux_tva_loyer ?? 18;
                                        $locataireId = $c->locataire_id ?? 0;
                                    @endphp
                                    <option value="{{ $c->id }}"
                                        data-loyer-nu="{{ $c->loyer_nu }}"
                                        data-charges="{{ $c->charges_mensuelles ?? 0 }}"
                                        data-tom="{{ $c->tom_amount ?? 0 }}"
                                        data-loyer-total="{{ $c->loyer_contractuel }}"
                                        data-taux-comm="{{ $c->bien?->taux_commission ?? 10 }}"
                                        data-bien="{{ $c->bien?->reference }} — {{ $c->bien?->adresse }}"
                                        data-locataire="{{ $c->locataire?->name }}"
                                        data-ref="{{ $c->reference_bail ?? 'BAIL-'.$c->id }}"
                                        data-brs-applicable="{{ $brsApplicable }}"
                                        data-taux-brs="{{ $tauxBrs }}"
                                        data-loyer-assujetti="{{ $loyerAssujetti }}"
                                        data-taux-tva="{{ $tauxTva }}"
                                        data-locataire-id="{{ $locataireId }}"
                                        {{ old('contrat_id', $contrat?->id) == $c->id ? 'selected':'' }}>
                                        {{ $c->reference_bail ?? 'BAIL-'.$c->id }}
                                        — {{ $c->bien?->reference }}
                                        — {{ $c->locataire?->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contrat_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        {{-- Infos contrat dynamiques --}}
                        <div id="contrat-details" style="display:none">
                            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;font-size:12px">
                                <div style="display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px solid #f3f4f6">
                                    <span style="color:#6b7280">Bien</span>
                                    <span style="font-weight:500;color:#0d1117" id="info-bien">—</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px solid #f3f4f6">
                                    <span style="color:#6b7280">Locataire</span>
                                    <span style="font-weight:500;color:#0d1117" id="info-locataire">—</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;padding:3px 0">
                                    <span style="color:#6b7280">Loyer contractuel</span>
                                    <span style="font-weight:700;color:#c9a84c;font-family:'Syne',sans-serif" id="info-loyer">—</span>
                                </div>
                            </div>

                            {{-- Badge BRS --}}
                            <div class="brs-badge" id="brs-badge">
                                <div class="brs-badge-title">
                                    ⚠ Retenue BRS applicable — Locataire entreprise (Art. 201 CGI SN)
                                </div>
                                <div class="brs-badge-body">
                                    Ce locataire est identifié comme une <strong>personne morale</strong>.
                                    Le BRS de <strong id="brs-taux-display">5%</strong> sera retenu sur le loyer TTC
                                    et versé directement à la DGI par le locataire.<br>
                                    Montant BRS estimé : <strong id="brs-montant-display">— F</strong> &nbsp;·&nbsp;
                                    Net à verser au propriétaire : <strong id="brs-net-display">— F</strong>
                                </div>
                                <div style="margin-top:6px">
                                    <a href="#" id="brs-profil-link" class="brs-badge-link">
                                        → Modifier le profil du locataire (décocher "Entreprise" si particulier)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PÉRIODE ET DATE --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="card-title">Période & Date</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Période concernée <span class="req">*</span></label>
                                <input type="month" name="periode" id="periode"
                                       class="form-input {{ $errors->has('periode') ? 'error':'' }}"
                                       value="{{ old('periode', now()->format('Y-m')) }}">
                                <div class="form-hint">Mois du loyer (pas la date de paiement)</div>
                                @error('periode')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date de paiement <span class="req">*</span></label>
                                <input type="date" name="date_paiement"
                                       class="form-input {{ $errors->has('date_paiement') ? 'error':'' }}"
                                       value="{{ old('date_paiement', $datePaiement) }}">
                                @error('date_paiement')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MONTANT --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="card-title">Montant</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Montant encaissé (FCFA) <span class="req">*</span></label>
                                <input type="number" name="montant_encaisse" id="montant_encaisse"
                                       class="form-input {{ $errors->has('montant_encaisse') ? 'error':'' }}"
                                       value="{{ old('montant_encaisse') }}" min="0" step="500"
                                       oninput="verifierMontant()">
                                <div class="montant-ok" id="montant-ok">✓ Correspond au loyer contractuel</div>
                                <div class="montant-diff" id="montant-diff">⚠ Différent du loyer contractuel</div>
                                @error('montant_encaisse')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Mode de paiement <span class="req">*</span></label>
                                <select name="mode_paiement" class="form-select {{ $errors->has('mode_paiement') ? 'error':'' }}">
                                    <option value="">— Choisir —</option>
                                    @foreach($modesPaiement as $val => $label)
                                        <option value="{{ $val }}" {{ old('mode_paiement') === $val ? 'selected':'' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mode_paiement')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Caution perçue <span class="opt">(premier mois)</span></label>
                                <input type="number" name="caution_percue"
                                       class="form-input" value="{{ old('caution_percue', 0) }}" min="0" step="500">
                                <div class="form-hint">Saisir uniquement au premier paiement</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Notes <span class="opt">(optionnel)</span></label>
                                <input type="text" name="notes" class="form-input"
                                       value="{{ old('notes') }}" placeholder="Ex: Paiement partiel…">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body" style="padding:14px 20px">
                        <div style="font-size:12px;color:#6b7280;margin-bottom:10px">
                            La commission, TVA et net propriétaire sont calculés automatiquement
                            à partir du taux de commission du bien.
                        </div>
                    </div>
                    <div class="submit-bar">
                        <a href="{{ route('admin.paiements.index') }}" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="20 6 9 17 4 12"/></svg>
                            Enregistrer le paiement
                        </button>
                    </div>
                </div>

            </div>{{-- fin colonne gauche --}}

            {{-- ═══ COLONNE DROITE : RÉCAPITULATIF ═══ --}}
            <div>
                <div class="recap-card">
                    <div class="recap-hd"><div class="recap-title">Décompte fiscal</div></div>
                    <div class="recap-body">

                        <div id="recap-vide" style="text-align:center;padding:20px 0;color:rgba(255,255,255,.25);font-size:12px">
                            Sélectionnez un contrat pour voir le décompte
                        </div>

                        <div id="recap-content" style="display:none">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.25);margin-bottom:8px">Loyer</div>
                            <div class="rp-row">
                                <div class="rp-lbl">Loyer nu</div>
                                <div class="rp-val" id="rp-loyer-nu">— F</div>
                            </div>
                            <div class="rp-row">
                                <div class="rp-lbl">Charges</div>
                                <div class="rp-val" id="rp-charges">— F</div>
                            </div>
                            <div class="rp-row">
                                <div class="rp-lbl">TOM</div>
                                <div class="rp-val" id="rp-tom">— F</div>
                            </div>
                            <div class="rp-row">
                                <div class="rp-lbl" style="color:rgba(255,255,255,.7)">Total loyer</div>
                                <div class="rp-val gold" id="rp-loyer-total">— F</div>
                            </div>

                            <div class="rp-sep"></div>
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.25);margin-bottom:8px">Commission agence</div>
                            <div class="rp-row">
                                <div class="rp-lbl">Taux</div>
                                <div class="rp-val" id="rp-taux">— %</div>
                            </div>
                            <div class="rp-row">
                                <div class="rp-lbl">Commission HT</div>
                                <div class="rp-val gold" id="rp-comm-ht">— F</div>
                            </div>
                            <div class="rp-row">
                                <div class="rp-lbl">TVA 18%</div>
                                <div class="rp-val" id="rp-tva">— F</div>
                            </div>
                            <div class="rp-row">
                                <div class="rp-lbl">Commission TTC</div>
                                <div class="rp-val gold" id="rp-comm-ttc">— F</div>
                            </div>

                            <div class="rp-sep"></div>
                            <div class="rp-total">
                                <div class="rp-total-lbl">Net propriétaire</div>
                                <div class="rp-total-val" id="rp-net">— FCFA</div>
                            </div>

                            {{-- Section BRS (cachée si non applicable) --}}
                            <div id="rp-brs-section" style="display:none;margin-top:10px;padding:10px 12px;background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);border-radius:8px">
                                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(248,113,113,.7);margin-bottom:6px">
                                    BRS — Retenue à la source (Art. 201)
                                </div>
                                <div class="rp-row" style="border-color:rgba(255,255,255,.05)">
                                    <div class="rp-lbl">Taux BRS</div>
                                    <div class="rp-val red" id="rp-brs-taux">— %</div>
                                </div>
                                <div class="rp-row" style="border-color:rgba(255,255,255,.05)">
                                    <div class="rp-lbl">Assiette (loyer TTC + TOM)</div>
                                    <div class="rp-val red" id="rp-brs-base">— F</div>
                                </div>
                                <div class="rp-row" style="border-bottom:none">
                                    <div class="rp-lbl" style="color:rgba(248,113,113,.8)">BRS retenu</div>
                                    <div class="rp-val red" id="rp-brs-montant">— F</div>
                                </div>
                            </div>

                            <div style="margin-top:10px;padding:10px 12px;background:rgba(74,222,128,.07);border:1px solid rgba(74,222,128,.15);border-radius:8px">
                                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(74,222,128,.5);margin-bottom:4px" id="rp-net-label">Net à verser</div>
                                <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#4ade80" id="rp-net-verser">— F</div>
                                <div style="font-size:10px;color:rgba(255,255,255,.25);margin-top:2px" id="rp-net-sublabel">Après commission TTC</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
const contrats = @json($contrats->keyBy('id'));
let loyerContractuel = 0;

function fmt(n) { return Math.round(n).toLocaleString('fr-FR') + ' F'; }

function chargerContrat(id) {
    if (!id) {
        document.getElementById('contrat-details').style.display = 'none';
        document.getElementById('recap-vide').style.display = 'block';
        document.getElementById('recap-content').style.display = 'none';
        return;
    }

    const select = document.getElementById('contrat_id');
    const opt    = select.options[select.selectedIndex];

    const loyerNu      = parseFloat(opt.dataset.loyerNu)       || 0;
    const charges      = parseFloat(opt.dataset.charges)        || 0;
    const tom          = parseFloat(opt.dataset.tom)             || 0;
    const total        = parseFloat(opt.dataset.loyerTotal)     || 0;
    const tauxComm     = parseFloat(opt.dataset.tauxComm)       || 10;
    const brsApplicable = parseInt(opt.dataset.brsApplicable)   === 1;
    const tauxBrs      = parseFloat(opt.dataset.tauxBrs)        || 15;
    const loyerAssujetti = parseInt(opt.dataset.loyerAssujetti) === 1;
    const tauxTva      = parseFloat(opt.dataset.tauxTva)        || 18;
    const locataireId  = opt.dataset.locataireId || '';
    loyerContractuel = total;

    // Calcul commission
    const commHt   = Math.round(loyerNu * tauxComm / 100);
    const tvaComm  = Math.round(commHt * 0.18);
    const commTtc  = commHt + tvaComm;
    const netProprio = total - commTtc;

    // Calcul BRS (assiette = loyer TTC + TOM)
    const loyerTtc = loyerAssujetti ? Math.round(loyerNu * (1 + tauxTva / 100)) : loyerNu;
    const brsBase  = loyerTtc + tom;
    const brsAmt   = brsApplicable ? Math.round(brsBase * tauxBrs / 100) : 0;
    const netAVerser = netProprio - brsAmt;

    // Infos contrat
    document.getElementById('info-bien').textContent      = opt.dataset.bien;
    document.getElementById('info-locataire').textContent = opt.dataset.locataire;
    document.getElementById('info-loyer').textContent     = fmt(total);
    document.getElementById('contrat-details').style.display = 'block';

    // Badge BRS
    const badge = document.getElementById('brs-badge');
    if (brsApplicable) {
        badge.style.display = 'block';
        document.getElementById('brs-taux-display').textContent  = tauxBrs + '%';
        document.getElementById('brs-montant-display').textContent = fmt(brsAmt);
        document.getElementById('brs-net-display').textContent   = fmt(netAVerser);
        if (locataireId) {
            document.getElementById('brs-profil-link').href = '/admin/users/' + locataireId + '/edit';
        }
    } else {
        badge.style.display = 'none';
    }

    // Recap fiscal
    document.getElementById('rp-loyer-nu').textContent    = fmt(loyerNu);
    document.getElementById('rp-charges').textContent     = fmt(charges);
    document.getElementById('rp-tom').textContent         = fmt(tom);
    document.getElementById('rp-loyer-total').textContent = fmt(total);
    document.getElementById('rp-taux').textContent        = tauxComm + ' %';
    document.getElementById('rp-comm-ht').textContent     = fmt(commHt);
    document.getElementById('rp-tva').textContent         = fmt(tvaComm);
    document.getElementById('rp-comm-ttc').textContent    = fmt(commTtc);
    document.getElementById('rp-net').textContent         = fmt(netProprio) + ' CFA';

    // Section BRS dans le recap
    const rpBrsSection = document.getElementById('rp-brs-section');
    if (brsApplicable) {
        rpBrsSection.style.display = 'block';
        document.getElementById('rp-brs-taux').textContent    = tauxBrs + ' %';
        document.getElementById('rp-brs-base').textContent    = fmt(brsBase);
        document.getElementById('rp-brs-montant').textContent = '−' + fmt(brsAmt);
        document.getElementById('rp-net-label').textContent    = 'Net à verser (après BRS)';
        document.getElementById('rp-net-sublabel').textContent = 'Après commission TTC et BRS';
    } else {
        rpBrsSection.style.display = 'none';
        document.getElementById('rp-net-label').textContent    = 'Net à verser';
        document.getElementById('rp-net-sublabel').textContent = 'Après commission TTC';
    }
    document.getElementById('rp-net-verser').textContent = fmt(netAVerser) + ' CFA';

    document.getElementById('recap-vide').style.display    = 'none';
    document.getElementById('recap-content').style.display = 'block';

    // Pré-remplir montant
    if (!document.getElementById('montant_encaisse').value) {
        document.getElementById('montant_encaisse').value = Math.round(total);
    }

    // Charger prochaine période via AJAX
    fetch(`/admin/paiements/dernier-periode/${id}`)
        .then(r => r.json())
        .then(data => {
            const periodeInput = document.getElementById('periode');
            if (data.periode) {
                periodeInput.value = data.periode.substring(0, 7);
            }
        });

    verifierMontant();
}

function verifierMontant() {
    const montant = parseFloat(document.getElementById('montant_encaisse').value) || 0;
    const ok   = document.getElementById('montant-ok');
    const diff = document.getElementById('montant-diff');

    if (!loyerContractuel || !montant) { ok.style.display='none'; diff.style.display='none'; return; }

    if (Math.abs(montant - loyerContractuel) < 1) {
        ok.style.display   = 'block';
        diff.style.display = 'none';
    } else {
        ok.style.display   = 'none';
        diff.style.display = 'block';
    }
}

// Init si contrat présélectionné
@if($contrat)
    document.addEventListener('DOMContentLoaded', () => {
        chargerContrat({{ $contrat->id }});
    });
@else
    const selectInit = document.getElementById('contrat_id');
    if (selectInit.value) {
        document.addEventListener('DOMContentLoaded', () => chargerContrat(selectInit.value));
    }
@endif
</script>
@endsection