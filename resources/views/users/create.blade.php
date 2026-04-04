<x-app-layout>
    <x-slot name="header">
        Nouveau {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
    </x-slot>

<style>
:root {
    --gold:#c9a84c; --gold-light:#f5e9c9; --gold-dark:#8a6e2f;
    --dark:#0d1117; --green:#16a34a; --red:#dc2626;
}

/* ── Layout principal ── */
.create-page { display:grid; grid-template-columns:260px 1fr; min-height:calc(100vh - 64px); }

/* ── Sidebar gauche ── */
.create-sidebar {
    background:var(--dark);
    padding:32px 24px;
    display:flex; flex-direction:column; gap:8px;
    border-right:1px solid rgba(255,255,255,.06);
    position:sticky; top:64px; height:calc(100vh - 64px); overflow-y:auto;
}
.sidebar-role {
    display:flex; align-items:center; gap:10px;
    padding:14px 16px; border-radius:12px;
    background:rgba(201,168,76,.12); border:1px solid rgba(201,168,76,.2);
    margin-bottom:20px;
}
.sidebar-role-icon { font-size:22px; }
.sidebar-role-lbl { font-size:10px; color:var(--gold); font-weight:700; text-transform:uppercase; letter-spacing:1px; }
.sidebar-role-name { font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:#fff; }

.nav-section { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:rgba(255,255,255,.25); padding:0 12px; margin-top:16px; margin-bottom:6px; }
.nav-item {
    display:flex; align-items:center; gap:10px;
    padding:10px 12px; border-radius:9px; cursor:pointer;
    font-size:12px; font-weight:500; color:rgba(255,255,255,.5);
    transition:all .15s; border:none; background:none; text-align:left; width:100%;
}
.nav-item:hover { background:rgba(255,255,255,.06); color:rgba(255,255,255,.8); }
.nav-item.active { background:rgba(201,168,76,.15); color:var(--gold); }
.nav-item-dot { width:6px; height:6px; border-radius:50%; background:rgba(255,255,255,.2); flex-shrink:0; transition:all .15s; }
.nav-item.active .nav-item-dot { background:var(--gold); }
.nav-item.done .nav-item-dot { background:var(--green); }
.nav-item.done { color:rgba(255,255,255,.4); }

.sidebar-tip {
    margin-top:auto; padding:14px 16px;
    background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08);
    border-radius:12px; font-size:11px; color:rgba(255,255,255,.4); line-height:1.6;
}

/* ── Zone formulaire ── */
.create-main { padding:32px 40px 60px; max-width:720px; }
.create-header { margin-bottom:32px; }
.create-title { font-family:'Syne',sans-serif; font-size:26px; font-weight:800; color:#0d1117; letter-spacing:-.5px; margin-bottom:6px; }
.create-sub { font-size:14px; color:#6b7280; }

/* ── Sections ── */
.form-section { margin-bottom:36px; }
.section-hd {
    display:flex; align-items:center; gap:12px;
    padding-bottom:14px; border-bottom:1px solid #f3f4f6; margin-bottom:20px;
}
.section-num {
    width:28px; height:28px; border-radius:8px;
    background:var(--dark); color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:800; font-family:'Syne',sans-serif;
    flex-shrink:0;
}
.section-num.gold { background:var(--gold); color:#0d1117; }
.section-title { font-family:'Syne',sans-serif; font-size:14px; font-weight:700; color:#0d1117; }
.section-desc { font-size:12px; color:#9ca3af; margin-top:1px; }

/* ── Champs ── */
.field-group { margin-bottom:16px; }
.field-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px; }
.field-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:16px; }
.field-label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
.field-req { color:var(--red); margin-left:2px; }
.field-opt { font-size:11px; font-weight:400; color:#9ca3af; margin-left:4px; }
.field-input {
    width:100%; padding:10px 13px;
    border:1.5px solid #e5e7eb; border-radius:10px;
    font-size:13px; color:#0d1117; font-family:'DM Sans',sans-serif;
    background:#fff; outline:none; transition:all .15s;
}
.field-input:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.1); }
.field-input.error { border-color:var(--red); }
.field-select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; background-size:14px; padding-right:36px; cursor:pointer; }
.field-hint { font-size:11px; color:#9ca3af; margin-top:5px; }
.field-error { font-size:11px; color:var(--red); margin-top:5px; }

/* ── Genre pills ── */
.genre-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.genre-pill input { position:absolute; opacity:0; width:0; height:0; }
.genre-pill { position:relative; }
.genre-pill label {
    display:flex; align-items:center; justify-content:center; gap:8px;
    padding:10px; border:1.5px solid #e5e7eb; border-radius:10px;
    cursor:pointer; font-size:13px; font-weight:500; color:#6b7280;
    background:#fff; transition:all .15s;
}
.genre-pill input:checked + label { border-color:var(--gold); background:var(--gold-light); color:var(--gold-dark); }
.genre-pill label:hover { border-color:var(--gold); }

/* ── Mode paiement pills ── */
.mode-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
.mode-pill input { position:absolute; opacity:0; width:0; height:0; }
.mode-pill { position:relative; }
.mode-pill label {
    display:flex; flex-direction:column; align-items:center; gap:4px;
    padding:10px 8px; border:1.5px solid #e5e7eb; border-radius:10px;
    cursor:pointer; font-size:11px; font-weight:500; color:#6b7280;
    background:#fff; transition:all .15s; text-align:center;
}
.mode-pill input:checked + label { border-color:var(--gold); background:var(--gold-light); color:var(--gold-dark); }
.mode-pill label:hover { border-color:var(--gold); }
.mode-emoji { font-size:18px; }

/* ── Type locataire pills ── */
.type-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:12px; }
.type-pill input { position:absolute; opacity:0; width:0; height:0; }
.type-pill { position:relative; }
.type-pill label {
    display:flex; flex-direction:column; align-items:center; gap:4px;
    padding:10px 8px; border:1.5px solid #e5e7eb; border-radius:10px;
    cursor:pointer; font-size:11px; font-weight:500; color:#6b7280;
    background:#fff; transition:all .15s; text-align:center;
}
.type-pill input:checked + label { border-color:var(--gold); background:var(--gold-light); color:var(--gold-dark); }

/* ── Alerte entreprise ── */
.enterprise-bloc {
    background:#fff1f2; border:1px solid #fecaca; border-radius:12px;
    padding:16px; margin-top:4px;
    display:none;
}
.enterprise-bloc.visible { display:block; }

/* ── Passwords ── */
.pwd-strength { height:4px; border-radius:2px; background:#f3f4f6; margin-top:8px; overflow:hidden; }
.pwd-fill { height:100%; border-radius:2px; transition:all .3s; }

/* ── Submit bar ── */
.submit-bar {
    position:sticky; bottom:0;
    background:rgba(255,255,255,.95); backdrop-filter:blur(8px);
    border-top:1px solid #e5e7eb; padding:16px 40px;
    display:flex; align-items:center; gap:12px; justify-content:flex-end;
    margin:0 -40px -60px;
}
.btn-submit {
    display:inline-flex; align-items:center; gap:8px;
    padding:12px 28px; background:var(--dark); color:#fff;
    border:none; border-radius:11px; font-size:14px; font-weight:600;
    font-family:'DM Sans',sans-serif; cursor:pointer; transition:all .15s;
}
.btn-submit:hover { background:#1a2332; }
.btn-submit svg { width:15px; height:15px; }
.btn-cancel {
    padding:12px 22px; background:#fff; color:#6b7280;
    border:1.5px solid #e5e7eb; border-radius:11px;
    font-size:13px; font-weight:500; font-family:'DM Sans',sans-serif;
    text-decoration:none; cursor:pointer; transition:all .15s;
}
.btn-cancel:hover { border-color:#9ca3af; }

/* ── Erreurs ── */
.error-banner {
    background:#fee2e2; border:1px solid #fecaca; border-radius:12px;
    padding:14px 18px; margin-bottom:24px;
}
.error-banner-title { font-size:13px; font-weight:600; color:var(--red); margin-bottom:6px; }
.error-banner-item { font-size:12px; color:#9f1239; display:flex; align-items:flex-start; gap:6px; margin-top:4px; }
</style>

<div class="create-page">

    {{-- ══ SIDEBAR ══════════════════════════════════════════════════════════ --}}
    <div class="create-sidebar">

        <div class="sidebar-role">
            <div class="sidebar-role-icon">{{ $role === 'proprietaire' ? '🏢' : '👤' }}</div>
            <div>
                <div class="sidebar-role-lbl">Création</div>
                <div class="sidebar-role-name">{{ $role === 'proprietaire' ? 'Propriétaire' : 'Locataire' }}</div>
            </div>
        </div>

        <div class="nav-section">Sections</div>

        <button class="nav-item active" onclick="scrollTo('sec-identite')">
            <div class="nav-item-dot"></div>
            Identité
        </button>
        <button class="nav-item" onclick="scrollTo('sec-acces')">
            <div class="nav-item-dot"></div>
            Accès & Mot de passe
        </button>
        @if($role === 'proprietaire')
        <button class="nav-item" onclick="scrollTo('sec-paiement')">
            <div class="nav-item-dot"></div>
            Mode de paiement
        </button>
        <button class="nav-item" onclick="scrollTo('sec-fiscal')">
            <div class="nav-item-dot"></div>
            Fiscal
        </button>
        @else
        <button class="nav-item" onclick="scrollTo('sec-type')">
            <div class="nav-item-dot"></div>
            Type & Statut fiscal
        </button>
        <button class="nav-item" onclick="scrollTo('sec-pro')">
            <div class="nav-item-dot"></div>
            Situation professionnelle
        </button>
        <button class="nav-item" onclick="scrollTo('sec-urgence')">
            <div class="nav-item-dot"></div>
            Contact d'urgence
        </button>
        @endif

        <div class="sidebar-tip">
            💡 Tous les champs marqués <strong style="color:var(--gold)">*</strong> sont obligatoires. Les autres peuvent être complétés plus tard depuis la fiche.
        </div>

    </div>

    {{-- ══ FORMULAIRE ════════════════════════════════════════════════════════ --}}
    <div class="create-main">

        <div class="create-header">
            <div class="create-title">
                Nouveau {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
            </div>
            <div class="create-sub">
                {{ $role === 'proprietaire'
                    ? 'Renseignez les informations du propriétaire. Il pourra accéder à son espace depuis son email.'
                    : 'Renseignez les informations du locataire. Il pourra consulter ses quittances depuis son espace.' }}
            </div>
        </div>

        @if($errors->any())
        <div class="error-banner">
            <div class="error-banner-title">Certains champs nécessitent votre attention</div>
            @foreach($errors->all() as $error)
            <div class="error-banner-item">
                <svg style="width:12px;height:12px;flex-shrink:0;margin-top:1px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $error }}
            </div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">

            {{-- ══ SECTION 1 : IDENTITÉ ══════════════════════════════════════ --}}
            <div class="form-section" id="sec-identite">
                <div class="section-hd">
                    <div class="section-num gold">1</div>
                    <div>
                        <div class="section-title">Identité personnelle</div>
                        <div class="section-desc">Informations de contact principales</div>
                    </div>
                </div>

                {{-- Nom --}}
                <div class="field-group">
                    <label class="field-label">Nom complet <span class="field-req">*</span></label>
                    <input type="text" name="name" class="field-input {{ $errors->has('name') ? 'error':'' }}"
                           value="{{ old('name') }}" placeholder="Ex: Moussa Diallo" autofocus>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- Email + Tél --}}
                <div class="field-grid-2">
                    <div>
                        <label class="field-label">Email <span class="field-req">*</span></label>
                        <input type="email" name="email" class="field-input {{ $errors->has('email') ? 'error':'' }}"
                               value="{{ old('email') }}" placeholder="email@exemple.com">
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="field-label">Téléphone</label>
                        <input type="text" name="telephone" class="field-input"
                               value="{{ old('telephone') }}" placeholder="+221 77 000 00 00">
                    </div>
                </div>

                {{-- Adresse --}}
                <div class="field-group">
                    <label class="field-label">Adresse <span class="field-opt">(optionnel)</span></label>
                    <input type="text" name="adresse" class="field-input"
                           value="{{ old('adresse') }}" placeholder="Rue, quartier...">
                </div>

                {{-- Genre --}}
                <div class="field-group">
                    <label class="field-label">Genre</label>
                    <div class="genre-grid">
                        <div class="genre-pill">
                            <input type="radio" name="genre" id="genre_homme" value="homme"
                                   {{ old('genre') === 'homme' ? 'checked':'' }}>
                            <label for="genre_homme">👨 Homme</label>
                        </div>
                        <div class="genre-pill">
                            <input type="radio" name="genre" id="genre_femme" value="femme"
                                   {{ old('genre') === 'femme' ? 'checked':'' }}>
                            <label for="genre_femme">👩 Femme</label>
                        </div>
                    </div>
                </div>

                {{-- Naissance + Nationalité + Ville --}}
                <div class="field-grid-3">
                    <div>
                        <label class="field-label">Date de naissance</label>
                        <input type="date" name="date_naissance" class="field-input"
                               value="{{ old('date_naissance') }}">
                    </div>
                    <div>
                        <label class="field-label">Nationalité</label>
                        <input type="text" name="nationalite" class="field-input"
                               value="{{ old('nationalite', 'Sénégalaise') }}">
                    </div>
                    <div>
                        <label class="field-label">Ville</label>
                        <select name="ville" class="field-input field-select">
                            @foreach(['Dakar','Thiès','Saint-Louis','Ziguinchor','Kaolack','Mbour','Rufisque','Touba','Diourbel','Tambacounda'] as $v)
                            <option value="{{ $v }}" {{ old('ville','Dakar') === $v ? 'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">CNI / Passeport <span class="field-opt">(optionnel)</span></label>
                    <input type="text" name="cni" class="field-input"
                           value="{{ old('cni') }}" placeholder="1 234 567 890 12">
                </div>
            </div>

            {{-- ══ SECTION 2 : ACCÈS ════════════════════════════════════════ --}}
            <div class="form-section" id="sec-acces">
                <div class="section-hd">
                    <div class="section-num">2</div>
                    <div>
                        <div class="section-title">Accès à l'espace personnel</div>
                        <div class="section-desc">Identifiants de connexion au portail {{ $role }}</div>
                    </div>
                </div>

                <div class="field-grid-2">
                    <div>
                        <label class="field-label">Mot de passe <span class="field-req">*</span></label>
                        <input type="password" name="password" id="pwd"
                               class="field-input {{ $errors->has('password') ? 'error':'' }}"
                               placeholder="Min. 8 caractères" oninput="checkPwd(this.value)">
                        <div class="pwd-strength"><div class="pwd-fill" id="pwd-bar" style="width:0%;background:var(--red)"></div></div>
                        <div class="field-hint" id="pwd-hint">Entrez un mot de passe</div>
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="field-label">Confirmer le mot de passe <span class="field-req">*</span></label>
                        <input type="password" name="password_confirmation" id="pwd2"
                               class="field-input" placeholder="Répétez le mot de passe"
                               oninput="checkConfirm()">
                        <div class="field-hint" id="pwd2-hint" style="color:transparent">—</div>
                    </div>
                </div>

                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;font-size:12px;color:#166534">
                    ✉️ Le {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }} pourra se connecter avec son email et ce mot de passe sur
                    <strong>{{ config('app.url') }}</strong>
                </div>
            </div>

            {{-- ══ SECTIONS PROPRIÉTAIRE ════════════════════════════════════ --}}
            @if($role === 'proprietaire')

            {{-- Section 3 : Mode de paiement --}}
            <div class="form-section" id="sec-paiement">
                <div class="section-hd">
                    <div class="section-num">3</div>
                    <div>
                        <div class="section-title">Mode de paiement préféré</div>
                        <div class="section-desc">Comment reverser les loyers nets au propriétaire</div>
                    </div>
                </div>

                <div class="mode-grid" style="margin-bottom:16px">
                    @foreach([
                        'virement'     => ['Virement',     '🏦'],
                        'wave'         => ['Wave',          '📱'],
                        'orange_money' => ['Orange Money',  '🟠'],
                        'especes'      => ['Espèces',       '💵'],
                        'cheque'       => ['Chèque',        '📝'],
                        'mobile_money' => ['Mobile Money',  '📲'],
                    ] as $val => [$lbl, $emoji])
                    <div class="mode-pill">
                        <input type="radio" name="mode_paiement_prefere" id="mode_{{ $val }}" value="{{ $val }}"
                               {{ old('mode_paiement_prefere','virement') === $val ? 'checked':'' }}>
                        <label for="mode_{{ $val }}">
                            <span class="mode-emoji">{{ $emoji }}</span>
                            {{ $lbl }}
                        </label>
                    </div>
                    @endforeach
                </div>

                <div class="field-grid-2">
                    <div>
                        <label class="field-label">Numéro Wave</label>
                        <input type="text" name="numero_wave" class="field-input"
                               value="{{ old('numero_wave') }}" placeholder="+221 77 XXX XX XX">
                    </div>
                    <div>
                        <label class="field-label">Numéro Orange Money</label>
                        <input type="text" name="numero_om" class="field-input"
                               value="{{ old('numero_om') }}" placeholder="+221 77 XXX XX XX">
                    </div>
                </div>

                <div class="field-grid-2">
                    <div>
                        <label class="field-label">Banque <span class="field-opt">(optionnel)</span></label>
                        <input type="text" name="banque" class="field-input"
                               value="{{ old('banque') }}" placeholder="CBAO, Ecobank, BIS...">
                    </div>
                    <div>
                        <label class="field-label">Numéro de compte <span class="field-opt">(optionnel)</span></label>
                        <input type="text" name="numero_compte" class="field-input"
                               value="{{ old('numero_compte') }}" placeholder="RIB / IBAN">
                    </div>
                </div>
            </div>

            {{-- Section 4 : Fiscal propriétaire --}}
            <div class="form-section" id="sec-fiscal">
                <div class="section-hd">
                    <div class="section-num">4</div>
                    <div>
                        <div class="section-title">Informations fiscales</div>
                        <div class="section-desc">NINEA et statut TVA du propriétaire</div>
                    </div>
                </div>

                <div class="field-grid-2">
                    <div>
                        <label class="field-label">NINEA <span class="field-opt">(optionnel)</span></label>
                        <input type="text" name="ninea" class="field-input"
                               value="{{ old('ninea') }}" placeholder="Ex: 00123456789">
                        <div class="field-hint">Numéro d'Identification National des Entreprises</div>
                    </div>
                    <div style="padding-top:26px">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:10px;transition:all .15s"
                               onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='#e5e7eb'">
                            <input type="checkbox" name="assujetti_tva" value="1"
                                   {{ old('assujetti_tva') ? 'checked':'' }}
                                   style="width:16px;height:16px;accent-color:var(--gold)">
                            <div>
                                <div style="font-size:13px;font-weight:500;color:#374151">Assujetti à la TVA</div>
                                <div style="font-size:11px;color:#9ca3af">Le propriétaire est redevable de TVA</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            @endif

            {{-- ══ SECTIONS LOCATAIRE ═══════════════════════════════════════ --}}
            @if($role === 'locataire')

            {{-- Section 3 : Type & Statut fiscal --}}
            <div class="form-section" id="sec-type">
                <div class="section-hd">
                    <div class="section-num">3</div>
                    <div>
                        <div class="section-title">Type de locataire & Statut fiscal</div>
                        <div class="section-desc">Détermine si la Retenue à la Source (BRS) s'applique</div>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Type de locataire</label>
                    <div class="type-grid">
                        @foreach([
                            'particulier' => ['Particulier',  '👤', false],
                            'entreprise'  => ['Entreprise',   '🏢', true],
                            'association' => ['Association',  '🤝', true],
                            'ambassade'   => ['Ambassade',    '🏛️', false],
                            'ong'         => ['ONG / Org.',   '🌍', false],
                        ] as $k => [$lbl, $ico, $brs])
                        <div class="type-pill">
                            <input type="radio" name="type_locataire" id="type_{{ $k }}" value="{{ $k }}"
                                   {{ old('type_locataire','particulier') === $k ? 'checked':'' }}
                                   onchange="onTypeChange('{{ $k }}', {{ $brs ? 'true':'false' }})">
                            <label for="type_{{ $k }}">
                                <span style="font-size:18px">{{ $ico }}</span>
                                {{ $lbl }}
                                @if($brs)<span style="font-size:9px;color:var(--red);font-weight:700">BRS 15%</span>@endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="est_entreprise" id="est_entreprise"
                           value="{{ in_array(old('type_locataire','particulier'),['entreprise','association']) ? '1':'0' }}">
                </div>

                {{-- Bloc entreprise --}}
                <div class="enterprise-bloc {{ in_array(old('type_locataire','particulier'),['entreprise','association']) ? 'visible':'' }}"
                     id="bloc-entreprise">
                    <div style="font-size:12px;font-weight:700;color:var(--red);margin-bottom:12px;display:flex;align-items:center;gap:6px">
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        BRS 15% — Retenue à la source automatique sur les paiements futurs
                    </div>
                    <div class="field-group">
                        <label class="field-label">Raison sociale</label>
                        <input type="text" name="nom_entreprise" class="field-input"
                               value="{{ old('nom_entreprise') }}" placeholder="Nom officiel de la société">
                    </div>
                    <div class="field-grid-2">
                        <div>
                            <label class="field-label">NINEA</label>
                            <input type="text" name="ninea_locataire" class="field-input"
                                   value="{{ old('ninea_locataire') }}" placeholder="00123456789" maxlength="30">
                        </div>
                        <div>
                            <label class="field-label">RCCM</label>
                            <input type="text" name="rccm_locataire" class="field-input"
                                   value="{{ old('rccm_locataire') }}" placeholder="SN-DKR-2024-B-XXXXX" maxlength="60">
                        </div>
                    </div>
                    <div style="max-width:180px">
                        <label class="field-label">Taux BRS personnalisé <span class="field-opt">(%)</span></label>
                        <input type="number" name="taux_brs_override" class="field-input"
                               value="{{ old('taux_brs_override') }}" placeholder="15" min="0" max="20" step="0.5">
                        <div class="field-hint">Vide = 15% légal</div>
                    </div>
                </div>
            </div>

            {{-- Section 4 : Situation professionnelle --}}
            <div class="form-section" id="sec-pro">
                <div class="section-hd">
                    <div class="section-num">4</div>
                    <div>
                        <div class="section-title">Situation professionnelle</div>
                        <div class="section-desc">Permet de calculer le taux d'effort locatif</div>
                    </div>
                </div>

                <div class="field-grid-3">
                    <div>
                        <label class="field-label">Profession</label>
                        <input type="text" name="profession" class="field-input"
                               value="{{ old('profession') }}" placeholder="Ex: Ingénieur">
                    </div>
                    <div>
                        <label class="field-label">Employeur</label>
                        <input type="text" name="employeur" class="field-input"
                               value="{{ old('employeur') }}" placeholder="Nom de l'employeur">
                    </div>
                    <div>
                        <label class="field-label">Revenu mensuel <span class="field-opt">(F)</span></label>
                        <input type="number" name="revenu_mensuel" class="field-input"
                               value="{{ old('revenu_mensuel') }}" placeholder="350000" min="0">
                    </div>
                </div>
            </div>

            {{-- Section 5 : Contact urgence --}}
            <div class="form-section" id="sec-urgence">
                <div class="section-hd">
                    <div class="section-num">5</div>
                    <div>
                        <div class="section-title">Contact d'urgence <span class="field-opt">(optionnel)</span></div>
                        <div class="section-desc">Personne à contacter en cas d'urgence</div>
                    </div>
                </div>

                <div class="field-grid-3">
                    <div>
                        <label class="field-label">Nom</label>
                        <input type="text" name="contact_urgence_nom" class="field-input"
                               value="{{ old('contact_urgence_nom') }}" placeholder="Prénom NOM">
                    </div>
                    <div>
                        <label class="field-label">Téléphone</label>
                        <input type="text" name="contact_urgence_tel" class="field-input"
                               value="{{ old('contact_urgence_tel') }}" placeholder="+221 7X XXX XX XX">
                    </div>
                    <div>
                        <label class="field-label">Lien</label>
                        <input type="text" name="contact_urgence_lien" class="field-input"
                               value="{{ old('contact_urgence_lien') }}" placeholder="Père, Conjoint...">
                    </div>
                </div>
            </div>

            @endif

            {{-- ══ BARRE SUBMIT ═════════════════════════════════════════════ --}}
            <div class="submit-bar">
                <a href="{{ $role === 'proprietaire' ? route('admin.users.proprietaires') : route('admin.users.locataires') }}"
                   class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Créer le {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
                </button>
            </div>

        </form>
    </div>
</div>

<script>
// ── Scroll navigation sidebar ──────────────────────────────────────────────
function scrollTo(id) {
    document.getElementById(id)?.scrollIntoView({ behavior:'smooth', block:'start' });
    // Mettre à jour l'état actif de la nav
    document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
    event.currentTarget.classList.add('active');
}

// ── Force du mot de passe ──────────────────────────────────────────────────
function checkPwd(v) {
    const bar = document.getElementById('pwd-bar');
    const hint = document.getElementById('pwd-hint');
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const levels = [
        { pct:'25%', color:'#dc2626', label:'Trop faible' },
        { pct:'50%', color:'#f59e0b', label:'Faible' },
        { pct:'75%', color:'#3b82f6', label:'Correct' },
        { pct:'100%',color:'#16a34a', label:'Fort ✓' },
    ];
    const l = levels[Math.max(0, score - 1)] ?? levels[0];
    if (v.length === 0) { bar.style.width='0%'; hint.textContent='Entrez un mot de passe'; hint.style.color='#9ca3af'; return; }
    bar.style.width = l.pct;
    bar.style.background = l.color;
    hint.textContent = l.label;
    hint.style.color = l.color;
    checkConfirm();
}

function checkConfirm() {
    const pwd = document.getElementById('pwd').value;
    const pwd2 = document.getElementById('pwd2').value;
    const hint = document.getElementById('pwd2-hint');
    if (!pwd2) { hint.style.color='transparent'; return; }
    if (pwd === pwd2) { hint.textContent='✓ Correspondent'; hint.style.color='#16a34a'; }
    else { hint.textContent='✗ Ne correspondent pas'; hint.style.color='#dc2626'; }
}

// ── Type locataire entreprise ──────────────────────────────────────────────
function onTypeChange(type, isBrs) {
    document.getElementById('est_entreprise').value = isBrs ? '1' : '0';
    const bloc = document.getElementById('bloc-entreprise');
    if (bloc) bloc.classList.toggle('visible', isBrs);
}
</script>
</x-app-layout>