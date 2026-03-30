<x-app-layout>
    <x-slot name="header">Nouveau {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}</x-slot>

    {{-- Header navigation --}}
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="{{ $role === 'proprietaire' ? route('admin.users.proprietaires') : route('admin.users.locataires') }}"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">
                Nouveau {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
            </h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                Renseignez les informations du {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
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

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}">

                    {{-- ══ Informations personnelles ══ --}}
                    <div style="margin-bottom:24px;">
                        <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                            👤 Informations personnelles
                        </div>

                        {{-- Nom --}}
                        <div style="margin-bottom:16px;">
                            <label class="form-label">Nom complet <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Prénom et Nom" class="input" required>
                            @error('name')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email + Téléphone --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                            <div>
                                <label class="form-label">Email <span style="color:#ef4444;">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       placeholder="exemple@email.com" class="input" required>
                                @error('email')
                                    <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone" value="{{ old('telephone') }}"
                                       placeholder="+221 77 000 00 00" class="input">
                            </div>
                        </div>

                        {{-- Adresse --}}
                        <div style="margin-bottom:16px;">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" value="{{ old('adresse') }}"
                                   placeholder="Rue, quartier..." class="input">
                        </div>

                        {{-- Mot de passe --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                            <div>
                                <label class="form-label">Mot de passe <span style="color:#ef4444;">*</span></label>
                                <input type="password" name="password"
                                       placeholder="Min. 8 caractères" class="input" required>
                                @error('password')
                                    <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label">Confirmer <span style="color:#ef4444;">*</span></label>
                                <input type="password" name="password_confirmation"
                                       placeholder="Répétez" class="input" required>
                            </div>
                        </div>
                    </div>

                    {{-- ══ Infos spécifiques propriétaire ══ --}}
                    @if($role === 'proprietaire')
                        <div style="margin-bottom:24px;">
                            <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                🏢 Informations propriétaire
                            </div>

                            {{-- Ville + NINEA --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Ville</label>
                                    <select name="ville" class="input">
                                        <option value="">— Choisir —</option>
                                        @foreach(['Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack', 'Mbour', 'Rufisque', 'Touba'] as $ville)
                                            <option value="{{ $ville }}" {{ old('ville') === $ville ? 'selected' : '' }}>{{ $ville }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">NINEA</label>
                                    <input type="text" name="ninea" value="{{ old('ninea') }}"
                                           placeholder="Numéro fiscal" class="input">
                                </div>
                            </div>

                            {{-- Mode paiement préféré --}}
                            <div style="margin-bottom:16px;">
                                <label class="form-label">Mode de paiement préféré</label>
                                <select name="mode_paiement_prefere" class="input">
                                    <option value="">— Choisir —</option>
                                    <option value="especes"      {{ old('mode_paiement_prefere') === 'especes'      ? 'selected' : '' }}>Espèces</option>
                                    <option value="virement"     {{ old('mode_paiement_prefere') === 'virement'     ? 'selected' : '' }}>Virement bancaire</option>
                                    <option value="wave"         {{ old('mode_paiement_prefere') === 'wave'         ? 'selected' : '' }}>Wave</option>
                                    <option value="orange_money" {{ old('mode_paiement_prefere') === 'orange_money' ? 'selected' : '' }}>Orange Money</option>
                                </select>
                            </div>

                            {{-- Wave + Orange Money --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Numéro Wave</label>
                                    <input type="text" name="numero_wave" value="{{ old('numero_wave') }}"
                                           placeholder="+221 77 000 00 00" class="input">
                                </div>
                                <div>
                                    <label class="form-label">Numéro Orange Money</label>
                                    <input type="text" name="numero_om" value="{{ old('numero_om') }}"
                                           placeholder="+221 77 000 00 00" class="input">
                                </div>
                            </div>

                            {{-- Banque --}}
                            <div style="margin-bottom:16px;">
                                <label class="form-label">Banque / RIB</label>
                                <input type="text" name="banque" value="{{ old('banque') }}"
                                       placeholder="Ex : CBAO — SN011 01100..." class="input">
                            </div>
                        </div>
                    @endif

                    {{-- ══ Infos spécifiques locataire ══ --}}
                    @if($role === 'locataire')
                        <div style="margin-bottom:24px;">
                            <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                📋 Informations locataire
                            </div>

                            {{-- Profession + CNI --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label">Profession</label>
                                    <input type="text" name="profession" value="{{ old('profession') }}"
                                           placeholder="Ex : Enseignant" class="input">
                                </div>
                                <div>
                                    <label class="form-label">N° CNI / Passeport</label>
                                    <input type="text" name="numero_cni" value="{{ old('numero_cni') }}"
                                           placeholder="Ex : 1 234 567 890 12" class="input">
                                </div>
                            </div>

                            {{-- Employeur --}}
                            <div style="margin-bottom:16px;">
                                <label class="form-label">Employeur / Garant</label>
                                <input type="text" name="employeur" value="{{ old('employeur') }}"
                                       placeholder="Nom de l'employeur ou garant" class="input">
                            </div>
                        </div>
                    @endif

                    {{-- Boutons --}}
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="{{ $role === 'proprietaire' ? route('admin.users.proprietaires') : route('admin.users.locataires') }}"
                           class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Créer le {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-app-layout>