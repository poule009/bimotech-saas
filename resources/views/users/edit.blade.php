<x-app-layout>
    <x-slot name="header">Modifier {{ $user->isProprietaire() ? 'le propriétaire' : 'le locataire' }}</x-slot>

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
.card-icon.red    { background:#fee2e2; } .card-icon.red svg    { color:#dc2626; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#0d1117; }
.card-body { padding:18px 20px; }
.form-group { margin-bottom:14px; }
.form-group:last-child { margin-bottom:0; }
.form-label { display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px; }
.req { color:#dc2626;margin-left:2px; }
.opt { font-size:11px;font-weight:400;color:#9ca3af;margin-left:4px; }
.form-input,.form-select,.form-textarea {
    width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;
    font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;
    background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;
}
.form-input:focus,.form-select:focus,.form-textarea:focus { border-color:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.10); }
.form-input.error { border-color:#dc2626; }
.form-textarea { resize:vertical;min-height:80px; }
.form-select { cursor:pointer; }
.form-row  { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.form-row3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px; }
.form-error { font-size:12px;color:#dc2626;margin-top:4px; }
.form-hint  { font-size:11px;color:#9ca3af;margin-top:4px; }
.toggle-row { display:flex;align-items:center;gap:10px;padding:10px 12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer;transition:border-color .15s; }
.toggle-row:hover { border-color:#c9a84c; }
.toggle-row input[type=checkbox] { width:16px;height:16px;accent-color:#c9a84c;cursor:pointer;flex-shrink:0; }
.submit-bar { display:flex;align-items:center;gap:10px;padding:14px 18px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-submit { flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:11px 20px;background:#0d1117;color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:opacity .15s; }
.btn-submit:hover { opacity:.88; }
.btn-submit svg { width:15px;height:15px; }
.btn-cancel { padding:11px 18px;background:#fff;color:#6b7280;border:1px solid #e5e7eb;border-radius:9px;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none; }

/* Mode paiement pills */
.mode-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:6px; }
.mode-pill input { position:absolute;opacity:0;width:0;height:0; }
.mode-pill { position:relative; }
.mode-pill label { display:flex;flex-direction:column;align-items:center;padding:8px 6px;border:1.5px solid #e5e7eb;border-radius:9px;cursor:pointer;font-size:10px;font-weight:500;color:#6b7280;text-align:center;gap:4px;background:#fff;transition:all .15s; }
.mode-pill input:checked + label { border-color:#c9a84c;background:#f5e9c9;color:#8a6e2f; }
.mode-pill label:hover { border-color:#c9a84c;background:#fdf8ef; }

/* sidebar */
.sidebar-sticky { position:sticky;top:80px; }
.user-card { background:#0d1117;border-radius:14px;padding:20px;margin-bottom:14px; }
.user-avatar { width:48px;height:48px;border-radius:12px;background:#c9a84c;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:#fff;margin:0 auto 12px; }
.user-name { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#fff;text-align:center; }
.user-email { font-size:11px;color:rgba(255,255,255,.4);text-align:center;margin-top:3px; }
.user-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:600;margin:10px auto 0;display:flex;justify-content:center; }
.badge-proprio { background:rgba(201,168,76,.15);color:#c9a84c;border:1px solid rgba(201,168,76,.2); }
.badge-locataire { background:rgba(29,78,216,.15);color:#93c5fd;border:1px solid rgba(29,78,216,.2); }

.info-row { display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:12px; }
.info-row:last-child { border-bottom:none; }
.info-lbl { color:rgba(255,255,255,.4); }
.info-val { color:rgba(255,255,255,.7);font-weight:500; }

/* type locataire pills */
.type-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:7px;margin-bottom:12px; }
.type-pill { position:relative; }
.type-pill input { position:absolute;opacity:0;width:0;height:0; }
.type-pill label { display:flex;flex-direction:column;align-items:center;padding:9px 6px;border:1.5px solid #e5e7eb;border-radius:9px;cursor:pointer;font-size:11px;font-weight:500;color:#6b7280;text-align:center;gap:4px;background:#fff;transition:all .15s; }
.type-pill input:checked + label { border-color:#c9a84c;background:#f5e9c9;color:#8a6e2f; }
.type-pill label:hover { border-color:#c9a84c;background:#fdf8ef; }
.brs-note { background:#fff1f2;border:1px solid #fecaca;border-radius:8px;padding:10px 12px;font-size:11px;color:#dc2626;line-height:1.6; }
</style>

<div style="padding:24px 32px 48px">

    {{-- BREADCRUMB --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="{{ $user->isProprietaire() ? route('admin.users.proprietaires') : route('admin.users.locataires') }}"
           style="color:#6b7280;text-decoration:none">
            {{ $user->isProprietaire() ? 'Propriétaires' : 'Locataires' }}
        </a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('admin.users.show', $user) }}" style="color:#6b7280;text-decoration:none">{{ $user->name }}</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Modifier</span>
    </div>

    <div style="margin-bottom:22px">
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
            Modifier — {{ $user->name }}
        </h1>
        <p style="font-size:13px;color:#6b7280;margin-top:3px">
            {{ $user->isProprietaire() ? 'Propriétaire' : 'Locataire' }} · {{ $user->email }}
        </p>
    </div>

    @if($errors->any())
    <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px">
        @foreach($errors->all() as $error)
            <div style="font-size:13px;color:#dc2626;display:flex;align-items:center;gap:6px">
                <svg style="width:13px;height:13px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                {{ $error }}
            </div>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" id="form-user">
        @csrf
        @method('PATCH')

        <div class="form-grid">
            <div>

                {{-- INFORMATIONS PERSONNELLES --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                        <div class="card-title">Informations personnelles</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Nom complet <span class="req">*</span></label>
                            <input type="text" name="name" class="form-input {{ $errors->has('name') ? 'error':'' }}"
                                value="{{ old('name', $user->name) }}" placeholder="Prénom et Nom">
                            @error('name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email <span class="req">*</span></label>
                                <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'error':'' }}"
                                    value="{{ old('email', $user->email) }}">
                                @error('email')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone" class="form-input"
                                    value="{{ old('telephone', $user->telephone) }}"
                                    placeholder="+221 7X XXX XX XX">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-input"
                                value="{{ old('adresse', $user->adresse) }}"
                                placeholder="Adresse complète">
                        </div>
                    </div>
                </div>

                {{-- ═══ PROPRIÉTAIRE ═══ --}}
                @if($user->isProprietaire())
                @php $profil = $user->proprietaire; @endphp

                {{-- Identité --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <div class="card-title">Identité</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">N° CNI / Passeport</label>
                                <input type="text" name="cni" class="form-input"
                                    value="{{ old('cni', $profil?->cni) }}" placeholder="1 234 567 890 12">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date de naissance</label>
                                <input type="date" name="date_naissance" class="form-input"
                                    value="{{ old('date_naissance', $profil?->date_naissance?->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Genre</label>
                                <select name="genre" class="form-select">
                                    <option value="">— Choisir —</option>
                                    <option value="homme" {{ old('genre', $profil?->genre) === 'homme' ? 'selected':'' }}>Homme</option>
                                    <option value="femme" {{ old('genre', $profil?->genre) === 'femme' ? 'selected':'' }}>Femme</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nationalité</label>
                                <input type="text" name="nationalite" class="form-input"
                                    value="{{ old('nationalite', $profil?->nationalite ?? 'Sénégalaise') }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Ville</label>
                                <input type="text" name="ville" class="form-input"
                                    value="{{ old('ville', $profil?->ville ?? 'Dakar') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Quartier</label>
                                <input type="text" name="quartier" class="form-input"
                                    value="{{ old('quartier', $profil?->quartier) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Adresse domicile</label>
                            <input type="text" name="adresse_domicile" class="form-input"
                                value="{{ old('adresse_domicile', $profil?->adresse_domicile) }}">
                        </div>
                    </div>
                </div>

                {{-- Paiement & Fiscal --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
                        <div class="card-title">Paiement & fiscal</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Mode de paiement préféré</label>
                            <div class="mode-grid">
                                @foreach([
                                    'virement'     => ['Virement', '🏦'],
                                    'wave'         => ['Wave', '📱'],
                                    'orange_money' => ['Orange Money', '🟠'],
                                    'especes'      => ['Espèces', '💵'],
                                    'cheque'       => ['Chèque', '📝'],
                                    'mobile_money' => ['Mobile Money', '📲'],
                                ] as $val => [$label, $icon])
                                <div class="mode-pill">
                                    <input type="radio" name="mode_paiement_prefere"
                                           id="mode_{{ $val }}" value="{{ $val }}"
                                           {{ old('mode_paiement_prefere', $profil?->mode_paiement_prefere) === $val ? 'checked':'' }}>
                                    <label for="mode_{{ $val }}">
                                        <span style="font-size:16px">{{ $icon }}</span>
                                        {{ $label }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Numéro Wave</label>
                                <input type="text" name="numero_wave" class="form-input"
                                    value="{{ old('numero_wave', $profil?->numero_wave) }}"
                                    placeholder="+221 77 XXX XX XX">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Numéro Orange Money</label>
                                <input type="text" name="numero_om" class="form-input"
                                    value="{{ old('numero_om', $profil?->numero_om) }}"
                                    placeholder="+221 77 XXX XX XX">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Banque</label>
                                <input type="text" name="banque" class="form-input"
                                    value="{{ old('banque', $profil?->banque) }}"
                                    placeholder="Ex: CBAO, Ecobank">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Numéro de compte</label>
                                <input type="text" name="numero_compte" class="form-input"
                                    value="{{ old('numero_compte', $profil?->numero_compte) }}"
                                    placeholder="RIB / IBAN">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">NINEA <span class="opt">(Numéro fiscal)</span></label>
                                <input type="text" name="ninea" class="form-input"
                                    value="{{ old('ninea', $profil?->ninea) }}"
                                    placeholder="Ex: 00123456789">
                            </div>
                            <div class="form-group" style="padding-top:22px">
                                <label class="toggle-row" for="assujetti_tva">
                                    <input type="checkbox" name="assujetti_tva" id="assujetti_tva" value="1"
                                        {{ old('assujetti_tva', $profil?->assujetti_tva) ? 'checked':'' }}>
                                    <div>
                                        <div style="font-size:13px;font-weight:500;color:#374151">Assujetti à la TVA</div>
                                        <div style="font-size:11px;color:#6b7280">Propriétaire redevable de TVA</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                @endif {{-- fin propriétaire --}}

                {{-- ═══ LOCATAIRE ═══ --}}
                @if($user->isLocataire())
                @php $profil = $user->locataire; @endphp

                {{-- Type de locataire --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                        <div class="card-title">Statut fiscal</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label" style="margin-bottom:10px">Type de locataire</label>
                            <div class="type-grid">
                                @foreach([
                                    'particulier' => ['Particulier', '👤', false],
                                    'entreprise'  => ['Entreprise',  '🏢', true],
                                    'association' => ['Association', '🤝', true],
                                    'ambassade'   => ['Ambassade',   '🏛️', false],
                                    'ong'         => ['ONG / Org.',  '🌍', false],
                                ] as $key => [$label, $icon, $brs])
                                <div class="type-pill">
                                    <input type="radio" name="type_locataire" id="type_{{ $key }}" value="{{ $key }}"
                                        {{ old('type_locataire', $profil?->type_locataire ?? 'particulier') === $key ? 'checked':'' }}
                                        onchange="onTypeChange('{{ $key }}', {{ $brs ? 'true':'false' }})">
                                    <label for="type_{{ $key }}">
                                        <span style="font-size:16px">{{ $icon }}</span>
                                        {{ $label }}
                                        @if($brs)<span style="font-size:9px;color:#dc2626;font-weight:700">BRS 15%</span>@endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="est_entreprise" id="est_entreprise"
                                value="{{ old('est_entreprise', in_array($profil?->type_locataire ?? 'particulier', ['entreprise','association']) ? '1':'0') }}">
                        </div>

                        {{-- Infos entreprise --}}
                        <div id="bloc-entreprise" style="{{ in_array($profil?->type_locataire ?? 'particulier', ['entreprise','association']) ? '':'display:none' }}">
                            <div style="background:#fef9ec;border:1px solid #fde68a;border-radius:10px;padding:14px;margin-top:4px">
                                <div class="form-group">
                                    <label class="form-label">Raison sociale</label>
                                    <input type="text" name="nom_entreprise" class="form-input"
                                        value="{{ old('nom_entreprise', $profil?->nom_entreprise) }}"
                                        placeholder="Nom officiel de l'entreprise">
                                    <div class="form-hint">Utilisé à la place du nom du représentant sur les quittances</div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">NINEA</label>
                                        <input type="text" name="ninea_locataire" class="form-input"
                                            value="{{ old('ninea_locataire', $profil?->ninea_locataire) }}"
                                            placeholder="Ex: 00123456789" maxlength="30">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">RCCM</label>
                                        <input type="text" name="rccm_locataire" class="form-input"
                                            value="{{ old('rccm_locataire', $profil?->rccm_locataire) }}"
                                            placeholder="SN-DKR-2024-B-XXXXX" maxlength="60">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Taux BRS personnalisé <span class="opt">(%)</span></label>
                                    <input type="number" name="taux_brs_override" class="form-input"
                                        value="{{ old('taux_brs_override', $profil?->taux_brs_override) }}"
                                        placeholder="15" min="0" max="20" step="0.5" style="width:120px">
                                    <div class="form-hint">Laisser vide = 15% légal. Saisir si convention fiscale (ex: 5%)</div>
                                </div>
                                <div class="brs-note" style="margin-top:4px">
                                    <strong>⚠ BRS automatique :</strong> Les paiements futurs incluront une retenue de <strong>15%</strong>
                                    sur le loyer TTC (Art. 196bis CGI SN). Les paiements passés ne sont pas modifiés.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Identité locataire --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <div class="card-title">Identité</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">N° CNI / Passeport</label>
                                <input type="text" name="cni" class="form-input"
                                    value="{{ old('cni', $profil?->cni) }}"
                                    placeholder="1 234 567 890 12">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date de naissance</label>
                                <input type="date" name="date_naissance" class="form-input"
                                    value="{{ old('date_naissance', $profil?->date_naissance?->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Genre</label>
                                <select name="genre" class="form-select">
                                    <option value="">— Choisir —</option>
                                    <option value="homme" {{ old('genre', $profil?->genre) === 'homme' ? 'selected':'' }}>Homme</option>
                                    <option value="femme" {{ old('genre', $profil?->genre) === 'femme' ? 'selected':'' }}>Femme</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nationalité</label>
                                <input type="text" name="nationalite" class="form-input"
                                    value="{{ old('nationalite', $profil?->nationalite ?? 'Sénégalaise') }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Ville</label>
                                <input type="text" name="ville" class="form-input"
                                    value="{{ old('ville', $profil?->ville ?? 'Dakar') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Quartier</label>
                                <input type="text" name="quartier" class="form-input"
                                    value="{{ old('quartier', $profil?->quartier) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Situation professionnelle --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg></div>
                        <div class="card-title">Situation professionnelle</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Profession</label>
                                <input type="text" name="profession" class="form-input"
                                    value="{{ old('profession', $profil?->profession) }}"
                                    placeholder="Ex: Fonctionnaire, Ingénieur">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Employeur</label>
                                <input type="text" name="employeur" class="form-input"
                                    value="{{ old('employeur', $profil?->employeur) }}"
                                    placeholder="Nom de l'employeur">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Revenu mensuel <span class="opt">(FCFA)</span></label>
                            <input type="number" name="revenu_mensuel" class="form-input"
                                value="{{ old('revenu_mensuel', $profil?->revenu_mensuel) }}"
                                placeholder="Ex: 350000" min="0" style="width:200px">
                            <div class="form-hint">Utilisé pour calculer le taux d'effort locatif</div>
                        </div>
                    </div>
                </div>

                {{-- Contact d'urgence --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gray"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.68A2 2 0 012 .5h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.32a16 16 0 006.29 6.29l1.18-1.18a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 15.92z"/></svg></div>
                        <div class="card-title">Contact d'urgence <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <div class="form-row3">
                            <div class="form-group">
                                <label class="form-label">Nom</label>
                                <input type="text" name="contact_urgence_nom" class="form-input"
                                    value="{{ old('contact_urgence_nom', $profil?->contact_urgence_nom) }}"
                                    placeholder="Prénom NOM">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="contact_urgence_tel" class="form-input"
                                    value="{{ old('contact_urgence_tel', $profil?->contact_urgence_tel) }}"
                                    placeholder="+221 7X XXX XX XX">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Lien</label>
                                <input type="text" name="contact_urgence_lien" class="form-input"
                                    value="{{ old('contact_urgence_lien', $profil?->contact_urgence_lien) }}"
                                    placeholder="Ex: Père, Conjoint">
                            </div>
                        </div>
                    </div>
                </div>

                @endif {{-- fin locataire --}}

                {{-- BOUTONS --}}
                <div class="card">
                    <div class="submit-bar">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>

            </div>{{-- fin colonne gauche --}}

            {{-- SIDEBAR --}}
            <div class="sidebar-sticky">
                <div class="user-card">
                    <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                    <div style="display:flex;justify-content:center">
                        <span class="user-badge {{ $user->isProprietaire() ? 'badge-proprio':'badge-locataire' }}">
                            {{ $user->isProprietaire() ? 'Propriétaire' : 'Locataire' }}
                        </span>
                    </div>
                    <div style="height:1px;background:rgba(255,255,255,.08);margin:14px 0"></div>
                    <div class="info-row">
                        <div class="info-lbl">Agence</div>
                        <div class="info-val">{{ $user->agency?->name ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-lbl">Créé le</div>
                        <div class="info-val">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                    @if($user->isLocataire())
                    <div class="info-row">
                        <div class="info-lbl">Type fiscal</div>
                        <div class="info-val" id="sidebar-type">{{ ucfirst($user->locataire?->type_locataire ?? 'particulier') }}</div>
                    </div>
                    @endif
                    @if($user->isProprietaire())
                    <div class="info-row">
                        <div class="info-lbl">Biens gérés</div>
                        <div class="info-val">{{ $user->biens()->count() }}</div>
                    </div>
                    @endif
                </div>

                {{-- Liens rapides --}}
                <div class="card">
                    <div style="padding:14px 18px">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:10px">Liens rapides</div>
                        <div style="display:flex;flex-direction:column;gap:6px">
                            <a href="{{ route('admin.users.show', $user) }}"
                               style="display:flex;align-items:center;gap:8px;font-size:12px;color:#374151;text-decoration:none;padding:7px 10px;border-radius:7px;border:1px solid #e5e7eb;transition:all .15s"
                               onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                               onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Voir la fiche
                            </a>
                            @if($user->isLocataire())
                            @php $contratActif = $user->contrats()->where('statut','actif')->first(); @endphp
                            @if($contratActif)
                            <a href="{{ route('admin.contrats.show', $contratActif) }}"
                               style="display:flex;align-items:center;gap:8px;font-size:12px;color:#374151;text-decoration:none;padding:7px 10px;border-radius:7px;border:1px solid #e5e7eb;transition:all .15s"
                               onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                               onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
                                Contrat actif
                            </a>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>

<script>
function onTypeChange(type, isBrs) {
    document.getElementById('est_entreprise').value = isBrs ? '1' : '0';
    document.getElementById('bloc-entreprise').style.display = isBrs ? '' : 'none';
    const sidebarType = document.getElementById('sidebar-type');
    if (sidebarType) {
        const labels = {
            particulier: 'Particulier', entreprise: 'Entreprise',
            association: 'Association', ambassade: 'Ambassade', ong: 'ONG / Org.'
        };
        sidebarType.textContent = labels[type] || type;
    }
}
</script>

</x-app-layout>