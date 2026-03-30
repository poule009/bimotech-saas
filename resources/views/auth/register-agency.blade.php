<x-guest-layout>
    <div class="auth-card">

        <div class="auth-card-title">Créer votre agence</div>
        <div class="auth-card-sub">Commencez votre essai gratuit de 30 jours — sans engagement</div>

        {{-- Erreurs --}}
        @if ($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;">
                @foreach ($errors->all() as $error)
                    <div style="font-size:13px;color:#dc2626;margin-bottom:2px;">❌ {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('agency.register.store') }}">
            @csrf

            {{-- ══ Section Agence ══ --}}
            <div style="margin-bottom:24px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:16px;">🏢</span>
                    <span style="font-size:13px;font-weight:700;color:#0f172a;letter-spacing:-.2px;">Votre agence</span>
                </div>

                {{-- Nom --}}
                <div class="form-group">
                    <label class="form-label">Nom de l'agence <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="agency_name" value="{{ old('agency_name') }}"
                           placeholder="Ex : Immobilier Prestige Dakar"
                           class="form-input" required>
                    @error('agency_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email + Téléphone --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                    <div>
                        <label class="form-label">Email <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="agency_email" value="{{ old('agency_email') }}"
                               placeholder="contact@monagence.sn"
                               class="form-input" required>
                        @error('agency_email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="agency_telephone" value="{{ old('agency_telephone') }}"
                               placeholder="+221 77 000 00 00"
                               class="form-input">
                    </div>
                </div>

                {{-- Adresse --}}
                <div class="form-group">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="agency_adresse" value="{{ old('agency_adresse') }}"
                           placeholder="Plateau, Dakar"
                           class="form-input">
                </div>
            </div>

            {{-- ══ Section Admin ══ --}}
            <div style="margin-bottom:24px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:16px;">👤</span>
                    <span style="font-size:13px;font-weight:700;color:#0f172a;letter-spacing:-.2px;">Votre compte administrateur</span>
                </div>

                {{-- Nom admin --}}
                <div class="form-group">
                    <label class="form-label">Nom complet <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}"
                           placeholder="Prénom et Nom"
                           class="form-input" required>
                    @error('admin_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email admin --}}
                <div class="form-group">
                    <label class="form-label">Email de connexion <span style="color:#ef4444;">*</span></label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                           placeholder="votre@email.com"
                           class="form-input" required>
                    @error('admin_email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                    <div>
                        <label class="form-label">Mot de passe <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="admin_password"
                               placeholder="Min. 8 caractères"
                               class="form-input" required>
                        @error('admin_password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Confirmer <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="admin_password_confirmation"
                               placeholder="Répétez"
                               class="form-input" required>
                    </div>
                </div>
            </div>

            {{-- CGU --}}
            <div style="margin-bottom:20px;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="cgu" value="1"
                           style="width:16px;height:16px;margin-top:2px;accent-color:#1a3c5e;cursor:pointer;flex-shrink:0;">
                    <span style="font-size:13px;color:#64748b;line-height:1.5;">
                        J'accepte les
                        <a href="#" style="color:#1a3c5e;font-weight:600;">conditions générales d'utilisation</a>
                        de la plateforme Bimotech.
                    </span>
                </label>
                @error('cgu')
                    <div class="form-error" style="margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Bouton --}}
            <button type="submit" class="btn-auth">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/>
                </svg>
                Créer mon agence gratuitement
            </button>

        </form>

    </div>

    {{-- Lien connexion --}}
    <div style="text-align:center;margin-top:16px;">
        <span style="font-size:13px;color:#64748b;">Déjà inscrit ?</span>
        <a href="{{ route('login') }}" class="auth-link" style="margin-left:6px;">
            Se connecter →
        </a>
    </div>

</x-guest-layout>