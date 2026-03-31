<x-app-layout>
    <x-slot name="header">Modifier {{ $user->role === 'proprietaire' ? 'le propriétaire' : 'le locataire' }}</x-slot>

    {{-- Header navigation --}}
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="{{ route('admin.users.show', $user) }}"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">
                Modifier — {{ $user->name }}
            </h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                {{ $user->role === 'proprietaire' ? 'Propriétaire' : 'Locataire' }} · {{ $user->email }}
            </p>
        </div>
    </div>

    <div style="max-width:680px;">
        <div class="card">
            <div class="card-body">

                {{-- Erreurs --}}
                @if($errors->any())
                    <div class="alert alert-error" style="margin-bottom:20px;">
                        @foreach($errors->all() as $error)
                            <div>❌ {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PATCH')

                    {{-- ══ Informations personnelles ══ --}}
                    <div style="margin-bottom:24px;">
                        <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                            👤 Informations personnelles
                        </div>

                        {{-- Nom --}}
                        <div style="margin-bottom:16px;">
                            <label class="form-label">Nom complet <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Prénom et Nom" class="input" required>
                            @error('name')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email + Téléphone --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                            <div>
                                <label class="form-label">Email <span style="color:#ef4444;">*</span></label>
                                <input type="email" name="email"
                                       value="{{ old('email', $user->email) }}"
                                       placeholder="exemple@email.com" class="input" required>
                                @error('email')
                                    <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone"
                                       value="{{ old('telephone', $user->telephone) }}"
                                       placeholder="+221 77 000 00 00" class="input">
                                @error('telephone')
                                    <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Adresse --}}
                        <div style="margin-bottom:16px;">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse"
                                   value="{{ old('adresse', $user->adresse) }}"
                                   placeholder="Rue, quartier..." class="input">
                        </div>
                    </div>

                    {{-- ══ Infos spécifiques propriétaire ══ --}}
                    @if($user->role === 'proprietaire')
                        <div style="margin-bottom:24px;">
                            <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                🏢 Informations propriétaire
                            </div>

                            @php $profil = $user->proprietaire; @endphp

                            {{-- Ville + Quartier --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Ville</label>
                                    <select name="ville" class="input">
                                        <option value="">— Choisir —</option>
                                        @foreach(['Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack', 'Mbour', 'Rufisque', 'Touba', 'Diourbel', 'Tambacounda', 'Kolda', 'Matam', 'Kédougou', 'Sédhiou', 'Fatick', 'Kaffrine', 'Louga'] as $ville)
                                        <option value="{{ $ville }}"
                                            {{ old('ville', $profil?->ville) === $ville ? 'selected' : '' }}>
                                            {{ $ville }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Quartier</label>
                                    <input type="text" name="quartier"
                                           value="{{ old('quartier', $profil?->quartier) }}"
                                           placeholder="Ex : Plateau, Almadies..." class="input">
                                </div>
                            </div>

                            {{-- CNI + Date naissance --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">N° CNI / Passeport</label>
                                    <input type="text" name="cni"
                                           value="{{ old('cni', $profil?->cni) }}"
                                           placeholder="Ex : 1 234 567 890 12" class="input">
                                </div>
                                <div>
                                    <label class="form-label">Date de naissance</label>
                                    <input type="date" name="date_naissance"
                                           value="{{ old('date_naissance', $profil?->date_naissance?->format('Y-m-d')) }}"
                                           class="input">
                                </div>
                            </div>

                            {{-- NINEA + Assujetti TVA --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">NINEA</label>
                                    <input type="text" name="ninea"
                                           value="{{ old('ninea', $profil?->ninea) }}"
                                           placeholder="Numéro fiscal" class="input">
                                </div>
                                <div style="display:flex;align-items:center;gap:10px;padding-top:24px;">
                                    <input type="hidden" name="assujetti_tva" value="0">
                                    <input type="checkbox" name="assujetti_tva" value="1" id="assujetti_tva"
                                           {{ old('assujetti_tva', $profil?->assujetti_tva) ? 'checked' : '' }}
                                           style="width:16px;height:16px;accent-color:var(--agency);">
                                    <label for="assujetti_tva" class="form-label" style="margin-bottom:0;cursor:pointer;">
                                        Assujetti à la TVA
                                    </label>
                                </div>
                            </div>

                            {{-- Mode paiement préféré --}}
                            <div style="margin-bottom:16px;">
                                <label class="form-label">Mode de paiement préféré</label>
                                <select name="mode_paiement_prefere" class="input">
                                    <option value="">— Choisir —</option>
                                    @foreach(['especes' => 'Espèces', 'virement' => 'Virement bancaire', 'wave' => 'Wave', 'orange_money' => 'Orange Money', 'free_money' => 'Free Money', 'cheque' => 'Chèque'] as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ old('mode_paiement_prefere', $profil?->mode_paiement_prefere) === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Wave + Orange Money --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Numéro Wave</label>
                                    <input type="text" name="numero_wave"
                                           value="{{ old('numero_wave', $profil?->numero_wave) }}"
                                           placeholder="+221 77 000 00 00" class="input">
                                </div>
                                <div>
                                    <label class="form-label">Numéro Orange Money</label>
                                    <input type="text" name="numero_om"
                                           value="{{ old('numero_om', $profil?->numero_om) }}"
                                           placeholder="+221 77 000 00 00" class="input">
                                </div>
                            </div>

                            {{-- Banque + Numéro compte --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Banque</label>
                                    <input type="text" name="banque"
                                           value="{{ old('banque', $profil?->banque) }}"
                                           placeholder="Ex : CBAO, Ecobank..." class="input">
                                </div>
                                <div>
                                    <label class="form-label">Numéro de compte</label>
                                    <input type="text" name="numero_compte"
                                           value="{{ old('numero_compte', $profil?->numero_compte) }}"
                                           placeholder="RIB / IBAN" class="input">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ══ Infos spécifiques locataire ══ --}}
                    @if($user->role === 'locataire')
                        <div style="margin-bottom:24px;">
                            <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                📋 Informations locataire
                            </div>

                            @php $profil = $user->locataire; @endphp

                            {{-- Profession + CNI --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Profession</label>
                                    <input type="text" name="profession"
                                           value="{{ old('profession', $profil?->profession) }}"
                                           placeholder="Ex : Enseignant" class="input">
                                </div>
                                <div>
                                    <label class="form-label">N° CNI / Passeport</label>
                                    <input type="text" name="cni"
                                           value="{{ old('cni', $profil?->cni) }}"
                                           placeholder="Ex : 1 234 567 890 12" class="input">
                                </div>
                            </div>

                            {{-- Employeur + Revenu mensuel --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Employeur</label>
                                    <input type="text" name="employeur"
                                           value="{{ old('employeur', $profil?->employeur) }}"
                                           placeholder="Nom de l'employeur" class="input">
                                </div>
                                <div>
                                    <label class="form-label">Revenu mensuel (FCFA)</label>
                                    <input type="number" name="revenu_mensuel"
                                           value="{{ old('revenu_mensuel', $profil?->revenu_mensuel) }}"
                                           min="0" step="5000" class="input">
                                </div>
                            </div>

                            {{-- Ville + Quartier --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Ville</label>
                                    <select name="ville" class="input">
                                        <option value="">— Choisir —</option>
                                        @foreach(['Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack', 'Mbour', 'Rufisque', 'Touba', 'Diourbel', 'Tambacounda', 'Kolda', 'Matam', 'Kédougou', 'Sédhiou', 'Fatick', 'Kaffrine', 'Louga'] as $ville)
                                        <option value="{{ $ville }}"
                                            {{ old('ville', $profil?->ville) === $ville ? 'selected' : '' }}>
                                            {{ $ville }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Quartier</label>
                                    <input type="text" name="quartier"
                                           value="{{ old('quartier', $profil?->quartier) }}"
                                           placeholder="Ex : Plateau, Almadies..." class="input">
                                </div>
                            </div>

                            {{-- Date naissance + Genre --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Date de naissance</label>
                                    <input type="date" name="date_naissance"
                                           value="{{ old('date_naissance', $profil?->date_naissance?->format('Y-m-d')) }}"
                                           class="input">
                                </div>
                                <div>
                                    <label class="form-label">Genre</label>
                                    <select name="genre" class="input">
                                        <option value="">— Choisir —</option>
                                        <option value="M" {{ old('genre', $profil?->genre) === 'M' ? 'selected' : '' }}>Masculin</option>
                                        <option value="F" {{ old('genre', $profil?->genre) === 'F' ? 'selected' : '' }}>Féminin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Boutons --}}
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Enregistrer les modifications
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-app-layout>
